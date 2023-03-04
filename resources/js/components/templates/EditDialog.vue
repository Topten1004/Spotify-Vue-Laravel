<template>
    <v-card class="edit-dialog page-dialog-wrapper">
        <div class="edit-dialog__header d-flex">
            <div class="edit-dialog__header__title">{{$t('Edit')}} {{ $t(editing) }}</div>
            <v-card-actions class="edit-dialog__header__actions">
                <slot name="header-actions"></slot>
                <v-btn text small @click="$emit('cancel')" :disabled="loading">
                    {{$t('Cancel')}}
                </v-btn>
                <v-btn
                    class="success"
                    :disabled="disableCallToAction || loading"
                    @click="$emit('callToAction')"
                    :loading="loading"
                    v-if="!hideCallToAction"
                >
                    <template v-slot:loader>
                        <span class="custom-loader">
                            <v-icon light>$vuetify.icons.cached</v-icon>
                        </span>
                    </template>

                    {{ $t(callToActionText) || $t("Save") }}
                </v-btn>
            </v-card-actions>
        </div>
        <div class="edit-dialog__body" :class="{ fullscreen }">
            <slot name="body"></slot>
        </div>
        <div class="edit-dialog__dialogs">
            <slot name="dialogs"></slot>
        </div>
    </v-card>
</template>
<script>
export default {
    props: [
        "loading",
        "callToActionText",
        "editing",
        "fullscreen",
        "disableCallToAction",
        "hideCallToAction"
    ]
};
</script>
<style lang="scss">
.edit-dialog {
    padding-bottom: 2em;
    &__header {
        box-shadow: 2px 0px 2px 2px rgb(0 0 0 / 15%);
        padding: 0.4em 1rem;
        display: flex;
        justify-content: space-between;
        &__title {
            display: flex;
            align-items: center;
            font-size: 1.25em;
        }
        &__actions {
            display: flex;
        }
    }
    &__body {
        padding: 2em 0;
        overflow-y: auto;
    }
    &__body.fullscreen {
        min-height: 100vh;
        padding-bottom: 10em;
    }
}
</style>
