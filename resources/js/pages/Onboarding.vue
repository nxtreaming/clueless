<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-md">
                <!-- Logo and App Name -->
                <div class="mb-8 text-center">
                    <div class="mb-4 flex items-center justify-center">
                        <div class="rounded-xl bg-gray-900 p-3 dark:bg-gray-100">
                            <svg class="h-10 w-10 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Welcome to Clueless</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">AI-powered meeting assistant</p>
                </div>

                <!-- Progress Indicator -->
                <div v-if="showSteps" class="mb-6 flex items-center justify-center space-x-2">
                    <div
                        v-for="i in 2"
                        :key="i"
                        :class="[
                            'h-1.5 w-20 rounded-full transition-all duration-300',
                            i <= currentStep ? 'bg-gray-900 dark:bg-gray-100' : 'bg-gray-200 dark:bg-gray-700',
                        ]"
                    ></div>
                </div>

                <!-- Main Card -->
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                    <div class="p-6">
                        <!-- Step 1: API Key -->
                        <div v-if="currentStep === 1">
                            <h2 class="mb-6 text-xl font-medium text-gray-900 dark:text-white">Setup OpenAI API</h2>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"> API Key </label>
                                    <div class="relative">
                                        <input
                                            v-model="apiKey"
                                            :type="showApiKey ? 'text' : 'password'"
                                            placeholder="sk-..."
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500"
                                            @input="validateApiKey"
                                        />
                                        <button
                                            @click="showApiKey = !showApiKey"
                                            type="button"
                                            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        >
                                            <svg v-if="!showApiKey" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <Transition
                                        enter-active-class="transition duration-200 ease-out"
                                        enter-from-class="opacity-0"
                                        enter-to-class="opacity-100"
                                        leave-active-class="transition duration-150 ease-in"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0"
                                    >
                                        <p v-if="apiKeyError" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                            {{ apiKeyError }}
                                        </p>
                                        <p v-else-if="apiKeyValid" class="mt-1.5 text-sm text-green-600 dark:text-green-400">
                                            ✓ Valid API key format
                                        </p>
                                    </Transition>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900/50">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Need an API key?
                                        <button
                                            @click="openOpenAI"
                                            class="font-medium text-gray-900 underline hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-300"
                                        >
                                            Get one from OpenAI
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button
                                    @click="saveApiKey"
                                    :disabled="!apiKeyValid || isValidating"
                                    :class="[
                                        'rounded-lg px-6 py-2.5 text-sm font-medium transition-all',
                                        apiKeyValid && !isValidating
                                            ? 'bg-gray-900 text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200'
                                            : 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-600',
                                    ]"
                                >
                                    <span v-if="isValidating" class="flex items-center">
                                        <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        Validating...
                                    </span>
                                    <span v-else>Continue</span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: GitHub Star -->
                        <div v-else-if="currentStep === 2">
                            <div class="text-center">
                                <div class="mb-4 inline-flex rounded-full bg-gray-100 p-3 dark:bg-gray-700">
                                    <svg class="h-8 w-8 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                        />
                                    </svg>
                                </div>
                                <h2 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">Support Clueless</h2>
                                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                                    If you find Clueless helpful, please consider starring our repository on GitHub. It helps others discover the
                                    project!
                                </p>

                                <button
                                    @click="openGitHub"
                                    class="mb-4 inline-flex items-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                                >
                                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8 .25a.75.75 0 01.673.418l1.882 3.815 4.21.612a.75.75 0 01.416 1.279l-3.046 2.97.719 4.192a.75.75 0 01-1.088.791L8 12.347l-3.766 1.98a.75.75 0 01-1.088-.79l.72-4.194L.818 6.374a.75.75 0 01.416-1.28l4.21-.611L7.327.668A.75.75 0 018 .25z"
                                        />
                                    </svg>
                                    Star on GitHub
                                </button>

                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                >
                                    <p v-if="hasStarred" class="mb-4 text-sm text-green-600 dark:text-green-400">✓ Thank you for your support!</p>
                                </Transition>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <button
                                    @click="currentStep = 1"
                                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    Back
                                </button>
                                <button
                                    @click="completeOnboarding"
                                    class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                                >
                                    Get Started
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const currentStep = ref(1);
const apiKey = ref('');
const showApiKey = ref(false);
const apiKeyValid = ref(false);
const apiKeyError = ref('');
const isValidating = ref(false);
const hasStarred = ref(false);

const showSteps = computed(() => currentStep.value > 0);

const validateApiKey = () => {
    const key = apiKey.value.trim();

    if (!key) {
        apiKeyValid.value = false;
        apiKeyError.value = '';
        return;
    }

    if (!key.startsWith('sk-')) {
        apiKeyValid.value = false;
        apiKeyError.value = 'API key should start with "sk-"';
        return;
    }

    if (key.length < 20) {
        apiKeyValid.value = false;
        apiKeyError.value = 'API key seems too short';
        return;
    }

    apiKeyValid.value = true;
    apiKeyError.value = '';
};

const saveApiKey = async () => {
    if (!apiKeyValid.value || isValidating.value) return;

    isValidating.value = true;

    try {
        const response = await axios.post('/api/openai/api-key', {
            api_key: apiKey.value,
        });

        if (response.data.success) {
            currentStep.value = 2;
        } else {
            apiKeyError.value = 'Failed to save API key. Please try again.';
        }
    } catch {
        apiKeyError.value = 'Invalid API key or connection error. Please check and try again.';
    } finally {
        isValidating.value = false;
    }
};

const openGitHub = async () => {
    try {
        // Use NativePHP API endpoint to open in default browser
        await axios.post('/api/open-external', {
            url: 'https://github.com/vijaythecoder/clueless',
        });
        hasStarred.value = true;
    } catch (error) {
        console.error('Failed to open GitHub:', error);
        // Fallback for web browser
        window.open('https://github.com/vijaythecoder/clueless', '_blank');
        hasStarred.value = true;
    }
};

const openOpenAI = async () => {
    try {
        // Use NativePHP API endpoint to open in default browser
        await axios.post('/api/open-external', {
            url: 'https://platform.openai.com/api-keys',
        });
    } catch (error) {
        console.error('Failed to open OpenAI:', error);
        // Fallback for web browser
        window.open('https://platform.openai.com/api-keys', '_blank');
    }
};

const completeOnboarding = () => {
    // Navigate to realtime agent
    router.visit('/realtime-agent');
};
</script>
