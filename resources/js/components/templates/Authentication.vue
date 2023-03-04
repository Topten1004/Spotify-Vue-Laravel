<template>
    <div class="auth-page-wrapper">
        <div class="white-layer">
            <v-card class="auth-box" elevation="8">
                <div class="align-justify-center">
                    <a class="logo-img-container" href="/">
                        <img
                            src="/images/favicon/logo.png"
                            :alt="$t('Logo Image')"
                        />
                    </a>
                </div>
                <slot></slot>
            </v-card>
        </div>
    </div>
</template>
<style lang="scss">
@import "../../../sass/player/pages/auth";
</style>
