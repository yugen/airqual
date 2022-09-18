import { createApp } from 'vue'
import App from '@/App.vue'
import '@/assets/app.css'

const app = createApp(App)

const registerComponents = (components => {
    Object.entries(components).forEach(([path, definition]) => {
        const componentName = path.split('/').pop().replace(/\.\w+$/, '')
        app.component(componentName, definition.default)
    })
})

registerComponents(import.meta.globEager('./components/*.vue'))
registerComponents(import.meta.globEager('./components/forms/*.vue'))

import {titleCase, sentenceCase} from '@/utils'

// app.config.globalProperties.append = (path, pathToAppend) =>
//   path + (path.endsWith('/') ? '' : '/') + pathToAppend

app.mixin({
        methods: {
            titleCase,
            sentenceCase
        }
    })
    .mount('#app')

