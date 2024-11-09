<template>
    <div v-if="form.progress" class="flex flex-row md:flex-col items-center justify-center h-screen">
        <p class="text-indigo-500">Sedang di upload & di compress</p>
        <div role="status mt-3">
            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor" />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill" />
            </svg>
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div v-else class="flex flex-row md:flex-col items-center justify-center h-screen">
        <div v-if="images && images.length">
            <!-- Menggunakan grid dengan responsif -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <div v-for="(i, index) in images" :key="`image-${index}`"
                    class="grid grid-rows-[auto,auto] md:w-60 border shadow-md cursor-pointer p-1">
                    <img :src="i.src" alt="Uploaded image"
                        class="w-full h-40 md:h-60 hover:opacity-50 transition duration-300 row-span-1">
                    <small class="text-center row-span-1 mt-1">
                        Original Size: {{ (i.original_size / 1024).toFixed(1) }} KB
                    </small>
                    <small class="text-center row-span-1 mt-1 text-green-600">
                        Compressed Size: {{ (i.compressed_size / 1024).toFixed(1) }} KB
                    </small>
                    <a :href="i.src" :download="`compressed_${index}.jpg`">
                        <button class="bg-green-400 border shadow-sm text-center text-white p-2 rounded-lg mt-2 w-full">
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