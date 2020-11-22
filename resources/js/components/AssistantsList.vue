<template>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <ul class="list-group">
                <template v-for="user in dataAssistants">
                    <a :class="`list-group-item list-group-item-action d-flex justify-content-between align-items-center ${user.pivot.accepted ? 'list-group-item-success' : ''}`">
                        {{ user.name }}
                        <span>
                            <i v-if="!parseInt(user.pivot.accepted)"
                               class="fas fa-check text-success cursor-pointer mx-2"
                               @click="setAccepted(user)"></i>
                            <i class="fas fa-times text-danger cursor-pointer mx-2" @click="remove(user.id)"></i>
                        </span>
                    </a>
                </template>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        assistants: {
            type: Array,
            required: true,
        },

        test: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            dataAssistants: this.$props.assistants,
        };
    },

    created() {
        this.sortAssistants();
    },

    methods: {
        sortAssistants() {
            this.dataAssistants.sort((a, b) => {
                return a.pivot.accepted && !b.pivot.accepted ? -1 : 0;
            });
        },

        setAccepted(user) {
            axios.post(`/tests/${this.$props.test.id}/accept-assistant/${user.id}`)
                .then(() => {
                    user.pivot.accepted = true;
                    this.sortAssistants();
                });
        },

        remove(userId) {
            this.$confirm("Naozaj chcete odstránit asistenta?", '', null, {
                confirmButtonText: 'Ano',
                cancelButtonText: 'Ne',
            }).then(() => {
                axios.post(`/tests/${this.$props.test.id}/remove-assistant/${userId}`)
                    .then(() => {
                        const userIndex = this.dataAssistants.findIndex(x => x.id === userId);
                        if (userIndex >= 0) {
                            this.dataAssistants.splice(userIndex, 1);
                        }

                        this.sortAssistants();
                    });
            }).catch(() => {});
        },
    },
}
</script>

<style scoped>

</style>
