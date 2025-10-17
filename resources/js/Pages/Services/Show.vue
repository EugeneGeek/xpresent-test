<template>
    <div class="min-h-screen bg-gradient-to-b from-sky-100 via-emerald-50 to-amber-50 p-8">
        <div class="max-w-3xl mx-auto bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-6 transition-all duration-300">
            <h2 class="text-3xl font-bold text-emerald-700 mb-6 text-center">
                {{ service.name }}
            </h2>

            <!-- Неделя -->
            <div class="flex gap-2 justify-center mb-8 flex-wrap">
                <button
                    v-for="day in daysOfWeek"
                    :key="day.date"
                    @click="selectDay(day)"
                    class="px-4 py-2 rounded-xl border font-medium transition-all duration-300"
                    :class="dayButtonClass(day)"
                >
                    {{ day.label }}
                </button>
            </div>

            <!-- Время -->
            <div v-if="selectedDate" class="text-center">
                <h3 class="text-lg font-semibold text-sky-800 mb-4">
                    Доступное время на {{ formatDate(selectedDate) }}
                </h3>

                <div v-if="loading" class="text-gray-500 italic">Загрузка...</div>

                <div v-else class="flex flex-wrap justify-center gap-3">
                    <button
                        v-for="time in slots"
                        :key="time"
                        class="px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-300"
                        :class="timeButtonClass(time)"
                        :disabled="isBooked(time) || isPastTime(time)"
                        @click="!isBooked(time) && !isPastTime(time) && book(time)"
                    >
                        {{ time }}
                        <span v-if="isPastTime(time)" class="text-xs block">прошло</span>
                    </button>
                </div>

                <p v-if="bookedTimes.length === slots.length && !loading" class="mt-4 text-red-600 font-semibold">
                    Все слоты на этот день заняты
                </p>
            </div>

            <!-- Ошибки -->
            <transition name="fade">
                <div
                    v-if="hasErrors"
                    class="mt-6 p-4 bg-red-50 border border-red-300 rounded-lg shadow-inner animate-pulse-slow"
                >
                    <h4 class="text-red-700 font-semibold mb-2">Ошибки при бронировании:</h4>
                    <ul class="list-disc list-inside text-red-600 text-sm">
                        <li v-for="(msg, key) in allErrors" :key="key">
                            <strong>{{ key }}:</strong> {{ Array.isArray(msg) ? msg[0] : msg }}
                        </li>
                    </ul>
                </div>
            </transition>

            <ModalSuccess v-if="showModal" @close="showModal = false" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ModalSuccess from "../Bookings/ModalSuccess.vue";
import { route } from "ziggy-js";

const props = defineProps({
    service: Object,
    bookings: Array, // начальные данные (например, за сегодня)
    serverTime: String
});

const page = usePage();
const selectedDate = ref(null);
const showModal = ref(false);
const extraErrors = ref({});
const currentBookings = ref(props.bookings || []);
const loading = ref(false);

const slots = ["10:00", "11:30", "13:00", "14:30", "16:00", "17:30", "19:00"];

// комбинируем ошибки Laravel + JSON
const allErrors = computed(() => ({
    ...(page.props.errors || {}),
    ...extraErrors.value,
}));
const hasErrors = computed(() => Object.keys(allErrors.value).length > 0);

// Серверное время
const serverDateTime = computed(() => new Date(props.serverTime));

// Неделя
const daysOfWeek = computed(() => {
    const start = new Date();
    const result = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        const dayOfWeek = d.getDay();
        const dateStr = d.toISOString().split("T")[0];
        result.push({
            date: dateStr,
            label: d.toLocaleDateString("ru-RU", { weekday: "short", day: "numeric" }),
            isWeekend: dayOfWeek === 0 || dayOfWeek === 6,
            isToday: isToday(dateStr),
        });
    }
    return result;
});

function isToday(dateStr) {
    const today = new Date();
    const compareDate = new Date(dateStr);
    return today.toDateString() === compareDate.toDateString();
}

function isPastTime(time) {
    if (!selectedDate.value) return false;
    const selectedDay = daysOfWeek.value.find(day => day.date === selectedDate.value);
    if (!selectedDay?.isToday) return false;

    const [hours, minutes] = time.split(':').map(Number);
    const slotTime = new Date(serverDateTime.value);
    slotTime.setHours(hours, minutes, 0, 0);

    return slotTime < serverDateTime.value;
}

const bookedTimes = computed(() => {
    return currentBookings.value.map(b => b.start_time.slice(0, 5));
});

function dayButtonClass(day) {
    return {
        "border-gray-300 text-gray-500 bg-gray-100 cursor-not-allowed": day.isWeekend,
        "border-sky-500 ring-2 ring-sky-500 bg-green-100": selectedDate.value === day.date,
        "hover:bg-emerald-100 hover:border-emerald-400": !day.isWeekend,
    };
}

function timeButtonClass(time) {
    const isPast = isPastTime(time);
    const isBookedTime = isBooked(time);
    return {
        "bg-red-100 text-red-600 border-red-300 cursor-not-allowed": isBookedTime || isPast,
        "bg-emerald-100 text-emerald-700 border-emerald-300 hover:bg-emerald-200": !isBookedTime && !isPast,
        "opacity-60": isPast,
    };
}

function isBooked(time) {
    return bookedTimes.value.includes(time);
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString("ru-RU", {
        weekday: "long",
        day: "numeric",
        month: "long",
    });
}

async function selectDay(day) {
    if (day.isWeekend) return;

    selectedDate.value = day.date;
    loading.value = true;

    try {
        const response = await router.visit(route("bookings.byDate", {
            service: props.service.id,
            date: day.date
        }), {
            preserveScroll: true,
            preserveState: true,
            only: ["bookings"],
            onSuccess: (page) => {
                currentBookings.value = page.props.bookings;
            },
            onFinish: () => {
                loading.value = false;
            }
        });
    } catch (e) {
        console.error("Ошибка загрузки слотов:", e);
        loading.value = false;
    }
}

function book(time) {
    if (isPastTime(time)) return;

    const name = prompt("Ваше имя:");
    const phone = prompt("Ваш телефон:");
    if (!name || !phone) return;

    extraErrors.value = {};

    router.post(
        route("bookings.store"),
        {
            service_id: props.service.id,
            date: selectedDate.value,
            start_time: time,
            client_name: name,
            client_phone: phone,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = true;
            },
            onError: (formErrors) => {
                console.warn("Ошибка валидации:", formErrors);
            },
            onFinish: (visit) => {
                const data = visit?.response?.data;
                if (data?.success === false && data.errors) {
                    extraErrors.value = data.errors;
                }
            },
        }
    );
}
</script>

<style scoped>
button:disabled {
    opacity: 0.6;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
