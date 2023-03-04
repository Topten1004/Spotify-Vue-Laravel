<template>
<div class="plans-wrapper">
    <Plan
        v-for="plan in plans"
        :key="plan.id"
        :plan="plan"
        @planChosen="$emit('plan', $event)"
        :chosen="false"
    />
</div>
</template>

<script>
import Plan from './Plan'
export default {
    props: ['plans'],
    components: {
        Plan
    }
};
</script>

<style lang="scss" scoped>
.plans-wrapper {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    padding-bottom: 1.2em;
}
</style>
