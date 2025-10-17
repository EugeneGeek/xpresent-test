<template>
    <div class="min-h-screen bg-gradient-to-b from-emerald-100 via-sky-100 to-amber-50 p-8">
        <div class="max-w-2xl mx-auto bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-6">
            <h1 class="text-3xl font-bold text-emerald-700 mb-6 text-center">
                Выберите туристическую услугу
            </h1>

            <ul class="space-y-4">
                <li
                    v-for="service in services"
                    :key="service.id"
                    class="group border border-emerald-200 rounded-xl p-4 hover:bg-emerald-50 hover:shadow-md transition-all duration-300 cursor-pointer"
                >
                    <Link
                        :href="`/services/${service.id}`"
                        class="flex justify-between items-center text-emerald-700 font-semibold text-lg"
                    >
                        <span>{{ service.name }}</span>
                        <span
                            class="text-sky-700 text-sm bg-sky-100 px-3 py-1 rounded-full group-hover:bg-sky-200"
                        >
              {{ service.duration }} мин
            </span>
                    </Link>
                </li>
            </ul>

            <div class="mt-8 text-center text-sm text-gray-500">
                Рабочее время: с <b>10:00</b> до <b>20:00</b> <br />
                В воскресенье бронирование недоступно
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({ services: Array });
</script>
