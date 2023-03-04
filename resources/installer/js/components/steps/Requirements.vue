<template>
    <v-container>
        <v-simple-table v-if="requirements.phpSupportInfo">
            <template v-slot:default>
                <thead>
                    <tr>
                        <th class="text-left" width="90%">
                            Requirement
                        </th>
                        <th class="text-left">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            PHP Version
                        </td>
                        <td>
                            <div
                                class="success--text bold"
                                v-if="
                                    requirements.phpSupportInfo.current >=
                                        requirements.phpSupportInfo.minimum
                                "
                            >
                                {{ requirements.phpSupportInfo.current }}
                            </div>
                            <div class="error--text bold" v-else>
                                {{ requirements.phpSupportInfo.current }}
                            </div>
                        </td>
                    </tr>
                    <tr v-if="requirements.requirements.requirements
                                        .apache">
                        <td>
                            mod_rewrite
                        </td>
                        <td class="text-center">
                            <v-icon
                                x-small
                                color="success"
                                v-if="requirements.requirements.requirements
                                        .apache.mod_rewrite
                                "
                            >
                                $vuetify.icons.check-circle-outline
                            </v-icon>
                            <v-icon x-small color="error" v-else>
                                $vuetify.icons.close-circle-outline
                            </v-icon>
                        </td>
                    </tr>
                    <tr
                        v-for="(requirement, i) in Object.keys(
                            requirements.requirements.requirements.php
                        )"
                        :key="i"
                    >
                        <td>
                            {{ requirement }}
                        </td>
                        <td class="text-center">
                            <v-icon
                                x-small
                                color="success"
                                v-if="
                                    requirements.requirements.requirements.php[
                                        requirement
                                    ]
                                "
                            >
                                $vuetify.icons.check-circle-outline
                            </v-icon>
                            <v-icon x-small color="error" v-else>
                                $vuetify.icons.close-circle-outline
                            </v-icon>
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
        <NextButton
            @clicked="$emit('next')"
            text="Next"
            :disabled="!areAllrequirementsMet"
        />
    </v-container>
</template>
<script>
import axios from "axios";
import NextButton from "../NextButton.vue";
export default {
    components: { NextButton },
    created() {
        axios.get("/api/installer/requirements").then(res => {
            this.requirements = res.data;
            this.areAllrequirementsMet =
                !Object.keys(
                    this.requirements.requirements.requirements.php).filter(
                        x => !this.requirements.requirements.requirements.php[x]
                    ).length
                 &&
                (this.requirements.requirements.requirements.apache ? 
                    this.requirements.requirements.requirements.apache.mod_rewrite:  true) &&
                this.requirements.phpSupportInfo.current >=
                    this.requirements.phpSupportInfo.minimum;
        });
    },
    data: () => ({
        requirements: {},
        areAllrequirementsMet: false
    })
};
</script>

<style></style>
