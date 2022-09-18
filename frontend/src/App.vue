<script setup>
// This starter template is using Vue 3 <script setup> SFCs
// Check out https://vuejs.org/api/sfc-script-setup.html#script-setup
import {ref, computed, onMounted, provide} from 'vue';
import {api} from './http';

const user = ref();
const fetchUser = async () => {
    console.log('fetching user');
    user.value = await api.get('/users/current')
                    .then(rsp => rsp.data)
                    .catch(error => console.log(error));
}
const clearUser = () => {
    user.value = null;
}
const isAuthed = computed(() => user.value && user.value.id);

provide({
    user,
    isAuthed
});

onMounted(() => {
    fetchUser()
});

</script>

<template>
  <div>
    <header id="nav" class="border-b bg-gray-100 print:hidden justify-between">
      <div class="container py-3 flex">
        <div id="main-menu" class="flex space-x-4 flex-grow">
            <div class="inline-block pr-4">
                <router-link to="/" class="black font-bold text-lg" title="Home">AirQual</router-link>
            </div>
        </div>
        <div>
            <div v-if="isAuthed">
                <a class="black block" href="/auth/logout" @click="clearUser">Logout</a>
            </div>
        </div>
      </div>
    </header>

    <div class="my-6 container">
      <div>
        <div v-if="isAuthed">
          <router-view :user="user" />
        </div>
        <ul v-else class="flex flex-col space-y-2">
            <li><a href="/auth/redirect?provider=github" class="btn blue">Login with Github</a></li>
            <li><a href="/auth/redirect?provider=google" class="btn blue">Login in Google</a></li>
        </ul>
      </div>
    </div>
  </div>
</template>

