<template>
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl mb-4">Бронирование успешно!</h2>
            <button class="px-4 py-2 bg-blue-500 text-white" @click="$emit('close')">ОК</button>
        </div>
    </div>
</template>
