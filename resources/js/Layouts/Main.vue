<template>
    <Header></Header>
    <main class="w-full md:w-2/3 mx-auto">
        <div v-if="flashSuccess" class="bg-green-500 text-white my-4 p-4 font-medium">
            {{ flashSuccess }}
        </div>
        <div v-if="flashError" class="bg-red-500 text-white my-4 p-4 font-medium">
            {{ flashError }}
        </div>
    </main>
    <slot></slot>
</template>

<script setup>
import Header from '@/Partials/Header.vue'
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue'

const page = usePage()

const flashSuccess = computed(
    () => page.props.flash.success
)

const flashError = computed(
    () => page.props.flash.error
)
</script>