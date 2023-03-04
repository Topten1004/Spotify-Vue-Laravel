<template>
    <v-card class="plan" rounded="lg" elevation="10">
        <div class="plan__recommended">
            <v-btn color="success" text small v-if="plan.recommended">{{
                $t("Recommended")
            }}</v-btn>
        </div>
        <div class="plan__name">
            {{ plan.name }}
        </div>
        <div class="plan__price">
            <div class="plan__price__currency" v-if="!plan.free">
                {{ planCurrencySymbol(plan) }}
            </div>
            <div class="plan__price__amount">
                <template v-if="plan.free">{{ $t("Free") }}</template>
                <template v-else>{{ price(plan.amount) }}</template>
            </div>
            <div class="plan__price__interval" v-if="!plan.free">
                / {{ plan.interval }}
            </div>
        </div>
        <div class="plan__features">
            <template v-if="plan.displayed_features">
                <div
                    class="plan__features__feature"
                    v-for="(feature, j) in plan.displayed_features"
                    :key="plan.id + '-feature-' + j"
                >
                    <div class="plan__features__feature__icon mr-1">
                        <v-icon color="success">$vuetify.icons.check</v-icon>
                    </div>
                    <div class="plan__features__feature__text">
                        {{ $t(feature) }}
                    </div>
                </div>
            </template>
        </div>
        <div class="plan__action">
            <template v-if="chosen">
                <v-btn
                    dense
                    text
                    color="primary"
                    outlined
                    :disabled="isSubscriptionLoading"
                    class="plan-chosen__header__change-btn"
                    @click="$emit('callToAction')"
                >
                    <v-icon small color="primary" class="mr-1"
                        >$vuetify.icons.arrow-left</v-icon
                    >
                    {{ $t("Change") }}
                </v-btn>
            </template>
            <template v-else>
                <v-btn
                    v-if="!isSubTo(plan.id)"
                    class="primary"
                    @click="$emit('planChosen', plan)"
                    >{{ $t("Choose") }}</v-btn
                >
                <v-btn v-else disabled outlined>{{ $t("Current Plan") }}</v-btn>
            </template>
        </div>
    </v-card>
</template>

<script>
export default {
    props: ["plan", "chosen", "isSubscriptionLoading"]
};
</script>

<style lang="scss" scoped>
.plan {
    margin-right: 1.5em;
    max-width: 300px;
    flex-basis: 30%;
    min-width: 200px;
    min-height: 400px;
    padding-bottom: 3em;
    margin-bottom: 1em;
    padding-top: 1em;
    position: relative;
    &__recommended {
        position: absolute;
        top: 1em;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
    }
    &__name {
        font-size: 2em;
        font-weight: bold;
        opacity: 1;
        text-align: center;
        font-weight: 400;
        padding: 0.8em 0.5em;
        padding-bottom: 0.5em;
    }
    &__price {
        display: flex;
        width: 100%;
        justify-content: center;
        &__currency {
            font-size: 1.4em;
            font-weight: bold;
            display: flex;
            align-items: flex-start;
            margin-top: -0.5em;
        }
        &__amount {
            font-weight: bold;
            font-size: 3em;
            line-height: 1;
        }
        &__interval {
            font-size: 1.2em;
            display: flex;
            align-items: flex-end;
            margin-left: 0.2em;
        }
    }
    &__features {
        padding: 1em 2em;
        &__feature {
            font-size: 1em;
            padding: 0.5em 0;
            display: flex;
            align-items: center;
        }
    }
    &__action {
        text-align: center;
        position: absolute;
        bottom: 1em;
        left: 0;
        right: 0;
    }
}
.plan__chosen {
    margin-right: 0;
    max-width: 1000px;
}
</style>
