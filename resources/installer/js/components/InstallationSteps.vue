<template>
<v-sheet max-width="1000px">
 <v-stepper v-model="steps">
        <v-stepper-header>
            <v-stepper-step :complete="steps > 1" step="1">
                Requirements
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="steps > 2" step="2">
                Permissions
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="steps > 3" step="3">
                Environement
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="steps > 4" step="4">
                Survey
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="steps > 5" step="5">
                Admin
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="steps > 6" step="6">
                Installation
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step step="7">
                Finish
            </v-stepper-step>
        </v-stepper-header>

        <v-stepper-items>
            <v-stepper-content step="1">
                <Requirements @next="steps++" />
            </v-stepper-content>

            <v-stepper-content step="2">
                <Permissions @next="steps++" />
            </v-stepper-content>

            <v-stepper-content step="3">
                <Environment @next="(config = $event, steps++)" />
            </v-stepper-content>

              <v-stepper-content step="4">
                <Survey @next="( survey = $event, steps++ )" />
            </v-stepper-content>

              <v-stepper-content step="5">
                <Account @startInstallation="( admin = $event, steps++ )" />
            </v-stepper-content>

              <v-stepper-content step="6">
                <Installation v-if="config && admin && survey" :config="config" :admin="admin" :survey="survey" @finished="steps++"/>
            </v-stepper-content>

              <v-stepper-content step="7">
                <Finish />
            </v-stepper-content>
        </v-stepper-items>
    </v-stepper>
</v-sheet>
</template>

<script>
import Account from './steps/Account.vue';
import Survey from './steps/Survey.vue';
import Permissions from './steps/Permissions.vue';
import Environment from './steps/Environment.vue';
import Installation from './steps/Installation.vue';
import Requirements from './steps/Requirements.vue';
import Finish from './steps/Finish.vue';
export default {
  components: { Requirements, Environment, Account, Permissions, Installation, Finish, Survey },
  data: () => ({
    steps: 1,
    config: null,
    admin: null,
    survey: null
  })
}
</script>