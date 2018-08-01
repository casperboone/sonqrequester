<template>
    <div>
        <div class="w-full bg-pink p-2 flex">
            <div class="flex-1">Outsite</div>
            <button :class="['p-2', 'text-pink', 'text-sm'].concat(formActive ? ['bg-pink-lighter'] : ['bg-white'])" @click="formActive = !formActive">Request a Song</button>
        </div>

        <request-form v-if="formActive" class="bg-white" @requestSubmitted="formActive = false"></request-form>
        <requests-list></requests-list>
    </div>
</template>

<script>
    import RequestForm from './RequestForm'
    import RequestsList from './RequestsList'

    export default {
        data() {
            return {
                formActive: false,
            }
        },
        components: {
            'request-form': RequestForm,
            'requests-list': RequestsList
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
