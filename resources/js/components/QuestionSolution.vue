<script>
export default {
    props: {
        questionSolution: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            dataSolution: this.$props.questionSolution,
            saved: true,
            errors: {},
        };
    },

    created() {
        this.mapOptions();
    },

    computed: {
        question() {
            return this.dataSolution.question;
        },

        selectedOptions() {
            let obj = {};
            this.dataSolution.options.forEach((option) => {
                obj[option] = true;
            });
            return obj;
        },
    },

    methods: {
        mapOptions() {
            this.dataSolution.options = this.dataSolution.options.map((x) => x.id);
        },

        clickOption(id) {
            const optionIndex = this.dataSolution.options.findIndex((x) => x === id);

            if (optionIndex === -1) {
                if (this.question.type === 0) {
                    //single select
                    this.dataSolution.options = [id];
                } else {
                    this.dataSolution.options.push(id);
                }
            } else {
                if (this.question.type === 0) {
                    //single select
                    this.dataSolution.options = [];
                } else {
                    this.dataSolution.options.splice(optionIndex, 1);
                }
            }
            this.saved = false;
            this.$forceUpdate();
        },

        save() {
            this.errors = {},
            this.saved = false;
            this.dataSolution.points = 0;
            axios.put(`/questionStudents/${this.dataSolution.id}`, this.dataSolution)
                .then((response) => {
                    this.dataSolution = response.data;
                    this.mapOptions();
                    this.saved = true;
                    this.$forceUpdate();
                })
                .catch((error) => {
                    this.$alert('Nepodařilo se uložit data');
                });
        },

        uploadFiles(event) {
            let component = this;
            if (!Array.isArray(component.dataSolution.files)) {
                component.dataSolution.files = [];
            }

            const enabledFiles = this.question.files_number;
            if (event.target.files.length + this.dataSolution.files.length > this.question.files_number) {
                this.$alert('Příliš mnoho nahranejch souborů');
                return;
            }

            [...event.target.files].forEach(file => {
                let reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = function () {
                    if (file.size > 2000000) {
                        component.$alert(`Soubor ${file.name} je příliš velký`);
                        return;
                    }

                    if (component.dataSolution.files.findIndex(x => x.name === file.name) !== -1) {
                        component.$alert(`Soubor ${file.name} již existuje`);
                        return;
                    }

                    component.dataSolution.files.push({
                        name: file.name,
                        base64: reader.result,
                        size: file.size,
                    });

                    component.saved = false;
                    component.$forceUpdate();
                };

                reader.onerror = function () {
                    component.$alert(`Soubor ${file.name} se nepodařilo načíst`);
                };
            });

            event.target.value = null;
        },

        removeFile(fileIndex) {
            this.dataSolution.files.splice(fileIndex, 1);
            this.$forceUpdate();
        },

        hasFileError(fileIndex) {
            return this.errors[`files.${fileIndex}.base64`] ||
                this.errors[`files.${fileIndex}.name`] ||
                this.errors[`files.${fileIndex}.size`]
        },
    },
}
</script>

<style scoped>

</style>
