<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12">
          <div class="title text-center py-3">
            Create Admin Account
          </div>
        </v-col>
      </v-row>
        <v-text-field
            v-model="email"
            :error-messages="emailErrors"
            @input="$v.email.$touch()"
            @blur="$v.email.$touch()"
            label="Email"
            outlined
        ></v-text-field>

        <v-text-field
            v-model="password"
            :error-messages="passwordErrors"
            @input="$v.password.$touch()"
            @blur="$v.password.$touch()"
            label="Password"
            type="password"
            outlined
        ></v-text-field>

        <v-text-field
            v-model="confirmPassword"
            :error-messages="confirmPasswordErrors"
            @input="$v.confirmPassword.$touch()"
            @blur="$v.confirmPassword.$touch()"
            label="Confirm Password"
            type="password"
            outlined
        ></v-text-field>
        <next-button @clicked="install" :text="'Create & Install'" :loading="installationLoading"></next-button>
    </v-container>
</template>

<script>
import axios from 'axios';
import { required, sameAs, minLength, email } from "vuelidate/lib/validators";
import NextButton from '../NextButton.vue';

export default {
  components: { NextButton },
    validations: {
        email: {
            required,
            email
        },
        password: {
            required,
            minLength: minLength(8)
        },
        confirmPassword: {
            sameAsPassword: sameAs("password")
        }
    },
    computed: {
        emailErrors() {
            const errors = [];
            if (!this.$v.email.$dirty) return errors;
            !this.$v.email.required && errors.push("Email is required");
            !this.$v.email.email && errors.push("Please enter a valid email");
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.password.$dirty) return errors;
            !this.$v.password.required && errors.push("Password is required");
            !this.$v.password.minLength && errors.push("At least 8 characters");
            return errors;
        },
        confirmPasswordErrors() {
            const errors = [];
            if (!this.$v.confirmPassword.$dirty) return errors;
            !this.$v.confirmPassword.sameAsPassword &&
                errors.push("Password confirmation does not match");
            return errors;
        }
    },
    data: () => ({
        email: "",
        password: "",
        confirmPassword: "",
        installationLoading: false
    }),

    methods: {
      install() {
        this.$v.$touch();
        if (!this.$v.$invalid) {
          this.$emit('startInstallation', {
            email: this.email,
            password: this.password,
            confirmPassword: this.confirmPassword
          })
        } else {
            this.loading = false;
        }
      }
    }
};
</script>

<style></style>
