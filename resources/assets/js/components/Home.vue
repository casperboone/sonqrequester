<template>
    <div>
        <requests-list></requests-list>
    </div>
</template>

<script>
    import RequestsList from './RequestsList'

    export default {
        components: {
            'requests-list': RequestsList
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
