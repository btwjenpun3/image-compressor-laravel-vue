<template>
    <div class="flex flex-row md:flex-col items-center justify-center h-screen">
        <div v-if="form.progress" class="">
            <p class="text-indigo-500">Sedang di upload & di compress</p>
            <progress :value="form.progress.percentage" max="100" class="">
                {{ form.progress.percentage }}%
            </progress>
        </div>
        <div v-else class="">
            <div v-if="images && images.length">
                <!-- Menggunakan grid dengan responsif -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div v-for="(i, index) in images" :key="`image-${index}`"
                        class="grid grid-rows-[auto,auto] w-60 border shadow-md cursor-pointer p-1">
                        <img :src="i.src" alt="Uploaded image"
                            class="w-full h-40 md:h-60 hover:opacity-50 transition duration-300 row-span-1">
                        <small class="text-center row-span-1 mt-1">
                            Original Size: {{ (i.original_size / 1024).toFixed(1) }} KB
                        </small>
                        <small class="text-center row-span-1 mt-1 text-green-600">
                            Compressed Size: {{ (i.compressed_size / 1024).toFixed(1) }} KB
                        </small>
                        <a :href="i.src" :download="`compressed_${index}.jpg`">
                            <button
                                class="bg-green-400 border shadow-sm text-center text-white p-2 rounded-lg mt-2 w-full">
                                Download
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div v-else class="cursor-pointer" @click="upload">
                <div class="text-center">
                    <div class="text-6xl text-blue-500 mb-4">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p class="text-xl font-medium mb-2">Klik disini untuk mengunggah</p>
                    <p class="text-gray-500 mb-2">Anda dapat <a href="#" class="text-blue-500">cari dari komputer
                            anda</a>
                        atau
                        <a href="#" class="text-blue-500">tambah URL gambar</a>.
                    </p>
                    <small class="text-yellow-600">* only support .jpeg and .jpg, maximum file size : 10 MB</small>
                    <input @input="addImages" @change="submitImages" type="file" ref="file" class="hidden" multiple>
                </div>
                <div v-if="errors && Object.keys(errors).length" class="text-red-500 bg-red-100 p-4 mt-3">
                    <small>Theres errors :</small>
                    <ul>
                        <li v-for="(error, index) in errors" :key="index"><small>{{ error }}</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue'

const page = usePage()

const props = defineProps({
    recaptcha_key: String,
    images: Array,
})

const errors = computed(
    () => page.props.errors
)

const form = useForm({
    images: [],
    recaptcha_token: null
})

// Menggunakan state lokal untuk images
const images = ref(props.images)

const file = ref(null)

const upload = () => {
    file.value.click()
}

const addImages = (event) => {
    for (const image of event.target.files) {
        form.images.push(image)
    }
}

// Load the reCAPTCHA script dynamically on mounted
onMounted(() => {
    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=${props.recaptcha_key}`;
    document.head.appendChild(script);
});

// Function to handle reCAPTCHA and get token
const getRecaptchaToken = () => {
    return new Promise((resolve, reject) => {
        grecaptcha.ready(() => {
            grecaptcha.execute(props.recaptcha_key, { action: 'submit' }).then((token) => {
                if (token) {
                    resolve(token); // Return token if successful
                } else {
                    reject('Failed to get reCAPTCHA token');
                }
            });
        });
    });
};

// Function to handle image submission
const submitImages = async () => {
    try {
        // Get the reCAPTCHA token
        const token = await getRecaptchaToken();

        // Set token to form
        form.recaptcha_token = token;

        // Post the form with the token
        form.post('/upload', {
            data: {
                images: form.images,
                recaptcha_token: token // Send token to server for verification
            },
            onSuccess: (response) => {
                images.value = response.props.images.map(image => ({
                    src: image.compressed_path,
                    compressed_size: image.compressed_size,
                    original_size: image.original_size
                }));
            },
            onError: () => {
                form.reset('images');
            }
        });
    } catch (error) {
        console.error('Error during reCAPTCHA token retrieval: ', error);
    }
};

</script>