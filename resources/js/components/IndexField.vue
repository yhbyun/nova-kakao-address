<template>
    <span class="text-90">{{ field.value }} <span v-if="field.lat">({{ field.lat }}, {{ field.lng }})</span></span>
</template>

<script>
export default {
    props: ['resourceName', 'field'],
}
</script>
