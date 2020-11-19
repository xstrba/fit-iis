<template>

</template>

<script>
export default {
    props: {
        questionSolutions: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            points: {},
            dataSolutions: this.$props.questionSolutions,
        };
    },

    computed: {
        pointsTotal() {
            let sum = 0;
            this.dataSolutions.forEach((sol) => {
                sum += parseFloat(parseFloat(sol.points || 0).toFixed(2));
            });

            return parseFloat(sum.toFixed(2));
        },
    },

    methods: {
        getOptionClass(solutionIndex, optionId) {
            const options = this.$props.questionSolutions[solutionIndex].options;
            const optionIndex = options.findIndex(x => x.id === optionId);
            if (optionIndex >= 0) {
                const option = options[optionIndex];
                if (option.points > 0) {
                    return 'list-group-item-success';
                }

                if (option.points < 0) {
                    return 'list-group-item-danger';
                }

                return 'list-group-item-warning';
            }

            return '';
        },
    },
}
</script>

<style scoped>

</style>
