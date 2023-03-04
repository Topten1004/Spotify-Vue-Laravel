<template>
    <div class="muzzie-nav">
        <v-app-bar color="white">
            <nav class="muzzie-nav__wrapper">
                <div class="logo">
                    <img
                        :src="$store.getters.getSettings.appLogoLight"
                        :alt="$t('Logo Image')"
                    />
                </div>
                <div class="options">
                    <div class="user" v-if="user">
                        <div class="account" @click="userMenu = !userMenu">
                            <div class="avatar">
                                <img
                                    :src="user.avatar"
                                    alt=""
                                    class="avatar-image"
                                />
                            </div>
                            <div class="name max-1-lines">
                                {{ user.name }}
                            </div>
                            <div class="chevron">
                                <v-icon light>$vuetify.icons.chevron-down</v-icon>
                            </div>
                            <abs-menu
                                :style="{
                                    top: '105%',
                                    width: '12em',
                                    right: '0'
                                }"
                                theme="light"
                                v-if="userMenu"
                            >
                                <user-menu
                                    :isAdmin="isAdmin(user)"
                                    :isArtist="isArtist(user)"
                                    :userId="user.id"
                                    @click:outside="userMenu = !userMenu"
                                />
                            </abs-menu>
                        </div>
                    </div>
                    <div class="auth-buttons" v-else>
                        <div class="buttons">
                            <v-btn
                                class="primary"
                                small
                                @click="$router.push({ name: 'login' })"
                                >{{ $t("Login") }}</v-btn
                            >
                            <v-btn
                                class="register__btn ml-2"
                                small
                                outlined
                                color="white"
                                v-if="
                                    !$store.getters.getSettings
                                        .disableRegistration
                                "
                                @click="$router.push({ name: 'register' })"
                                >{{ $t("Register") }}</v-btn
                            >
                        </div>
                    </div>
                </div>
            </nav>
        </v-app-bar>
    </div>
</template>

<script>
import SubButton from '../elements/single-items/SubButton'
export default {
    components: {
        SubButton
    },
    data() {
        return {
            userMenu: false
        };
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        }
    }
};
</script>

<style lang="scss">
.muzzie-nav {
    z-index: 2;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    &__wrapper {
        padding: 1em 1.5em;
        display: flex;
        width: 100%;
        justify-content: space-between;
        align-items: center;
    }
    .logo {
        padding-left: 1em;
        z-index: 2;
        img {
            max-height: 3em;
        }
    }
    .options {
        display: flex;
        align-items: center;
        z-index: 2;
    }
}
</style>
