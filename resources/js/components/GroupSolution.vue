<script>
import QuestionSolution from "./QuestionSolution";

export default {
    components: {
        QuestionSolution,
    },

    props: {
        groupSolution: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            alertOn: false,
        };
    },

    created() {
        setInterval(this.checkTime, 1000);
    },

    computed: {
        group() {
            return this.$props.groupSolution.group;
        },

        endTime() {
            return Date.parse(this.group.test.end_date);
        },
    },

    methods: {
        checkTime() {
            if (Date.now() < this.endTime && !this.alertOn) {
                this.alertOn = true;
                this.$fire({text: "Test ukončen",
                    confirmButtonText: 'Ok',
                })
                .then(() => {
                    this.finish(false);
                })
                .catch(() => {
                    this.finish(false);
                });
            }
        },

        finish(alert = true) {
            if (alert) {
                this.$confirm("Naza si přejete ukončit test?", '', null, {
                    confirmButtonText: 'Ano',
                    cancelButtonText: 'Ne',
                })
                .then(() => {
                    this.finishSend();
                })
                .catch(() => {});
            } else {
                this.finishSend();
            }
        },

        finishSend() {
            axios.post(`/tests/${this.group.test.id}/finish`)
                .then((response) => {
                    window.location.replace(response.data.redirect);
                })
                .catch(() => {
                    this.$alert("Nepodařilo se ukončit test");
                });
        },
    },
}
</script>

<style scoped>

</style>
