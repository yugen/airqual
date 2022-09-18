<script setup>
    import { computed, ref, watch } from 'vue'
    import {api, isValidationError} from '@/http'

    const props = defineProps({
        user: {
            type: Object,
            required: false
        }
    })

    const emits = defineEmits(['settingsUpdated'])

    const assessment = ref();
    const settings = ref({location: null, threshold: null});
    const errors = ref({});

    const hasSettings = computed(() => settings.location && settings.threshold);
    const assessmentTitleColor = computed(() => {
        if (!hasSettings.value && !assessment.value) {
            return 'text-black'
        }
        if (settings.value.threshold <= assessment.value.aqi) {
            return 'text-red-700'
        }
        return 'text-green-600'
    })

    const postSettings = async () => {
        try {
            assessment.value = await api.post('/settings', settings.value)
                                .then(rsp => rsp.data)
            emits('settingsUpdated')
        } catch (err) {
            if (isValidationError(err)) {
                this.errors.value = err.response.data.errors;
            }
        }
    }

    const getAssessment = async () => {
        try {
            assessment.value = await api.get('/air-quality-assessment')
                                .then(rsp => rsp.data)
        } catch (e) {
            alert('there was a problem fetching air quality data.')
        }
    }

    watch(() => props.user, (to) => {
        settings.value = {
            location: to.location,
            threshold: to.threshold
        }
        getAssessment()
    }, {immediate: true})
</script>
<template>
    <div>
        <section v-if="assessment">
            <h2 :class="assessmentTitleColor">Air Quality Assessment</h2>
            <ObjectDictionary :obj="assessment"></ObjectDictionary>
        </section>
        <hr>
        <br>
        <section class="bg-gray-200 p-4 rounded-lg">
            <h3>Set your location &amp; AQ threshold</h3>
            <div>
                <input-row label="location" v-model="settings.location" :errors="errors.location"></input-row>
                <input-row label="threshold" v-model="settings.threshold" :errors="errors.threshold"></input-row>
                <button @click="postSettings" class="block mt-2">Save Settings</button>
            </div>
        </section>
    </div>
</template>
