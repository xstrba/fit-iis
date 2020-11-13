<template>
    <div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="new-group-name">Název nové skupiny</label>
                <input type="text" class="form-control" id="new-group-name" v-model="newGroupName" onkeydown="return event.key !== 'Enter';">
            </div>
            <div class="col-12 form-group">
                <button class="btn btn-primary" @click="addNewGroup()">Přidat</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <a v-for="(group, index) in groups" :class="`mr-1 btn ${index === activeGroupId ? 'btn-success' : 'btn-outline-success'}`"
                        @click="activeGroupId = index">
                    {{ group.name }}
                </a>
            </div>
        </div>

        <div class="row" v-if="activeGroup">
            <div class="col-12">
                <div class="py-2 px-4 border-top border-success">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-danger" @click="removeActiveGroup()">Smazat skupinu</button>
                        </div>

                        <div class="col-12 form-group mt-4">
                            <label for="active-group-name">Název skupiny</label>
                            <input type="text" class="form-control" id="active-group-name"
                                   v-model="activeGroup.name" onkeydown="return event.key !== 'Enter';">
                        </div>

                        <div class="col-12 my-2">
                            <h3 class="h3">Otázky</h3>
                        </div>

                        <div class="col-12 form-group">
                            <button class="btn btn-primary" @click="saveActiveGroup()">Uložit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import QuestionForm from "./QuestionForm";

export default {
    components: {
        QuestionForm,
    },

    props: {
        groups: {
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
            dataGroups: this.$props.groups,
            newGroupName: '',
            activeGroupId: 0,
        };
    },

    computed: {
        activeGroup() {
            if (this.dataGroups[this.activeGroupId]) {
                return this.dataGroups[this.activeGroupId];
            }
            return null;
        },
    },

    methods: {
        addNewGroup() {
            if (this.newGroupName && this.newGroupName.length) {
                axios.post(`/groups`, {
                    name: this.newGroupName,
                    test_id: this.$props.test.id,
                })
                .then((response) => {
                    this.dataGroups.push(response.data);
                    this.newGroupName = '';
                })
                .catch((error) => {
                    this.$alert('Nepovedlo se vytvořit skupinu. Zkuste znovu.');
                });
            }
        },

        saveActiveGroup() {
            if (this.activeGroup) {
                axios.put(`/groups/${this.activeGroup.id}`, this.activeGroup)
                    .then((response) => {
                        this.$alert('Skupina uložena');
                        this.dataGroups[this.activeGroupId] = response.data;
                    })
                    .catch((error) => {
                        this.$alert('Nepovedlo se uložit skupinu. Zkuste znovu.');
                    });
            }
        },

        removeActiveGroup() {
            if (this.activeGroup) {
                this.$confirm("Naozaj chcete odstránit skupinu?", '', null, {
                    confirmButtonText: 'Ano',
                    cancelButtonText: 'Ne',
                })
                    .then(() => {
                        axios.delete(`/groups/${this.activeGroup.id}`)
                            .then(() => {
                                this.dataGroups.splice(this.activeGroupId, 1);
                                this.activeGroupId = 0;
                            })
                            .catch(() => {
                                this.$alert('Nepovedlo se odstránit skupinu. Zkuste znovu.');
                            });
                    })
                    .catch(() => {
                    });
            }
        },
    },
}
</script>

<style scoped>

</style>
