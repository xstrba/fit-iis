<template>
    <div>
        <div class="row">
            <div class="col-12 my-2" v-for="(question, index) in dataQuestions" :key="index">
                <div :class="{card: true, 'border-danger': hasQuestionError(index)}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 :class="{h4: true, 'm-0': true, 'text-danger': hasQuestionError(index)}">
                            {{ index + 1 }}. {{ question.name }}
                        </h4>
                        <i class="fas fa-trash cursor-pointer text-danger" @click="removeQuestion(index)"></i>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label :for="`question-${index}`">Název otázky <span class="required">*</span></label>
                            <input type="text" :id="`question-${index}`"
                                   v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.name`]}"
                                   v-model="question.name"
                                   onkeydown="return event.key !== 'Enter';">
                            <small v-if="!!errors[`questions.${index}.name`]" class="form-text error">{{ errors[`questions.${index}.name`][0] }}</small>
                        </div>
                        <div class="form-group">
                            <label :for="`question-dsc-${index}`">Text otázky</label>
                            <textarea :id="`question-dsc-${index}`"
                                      v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.text`]}"
                                      v-model="question.text">
                            </textarea>
                            <small v-if="!!errors[`questions.${index}.text`]" class="form-text error">{{ errors[`questions.${index}.text`][0] }}</small>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <ul class="list-group">
                                    <li v-for="(file, fileIndex) in question.files"
                                        :key="`files-${fileIndex}`"
                                        class="list-group-item d-flex justify-content-between align-items-center">
                                        <span v-bind:class="{'text-danger': hasFileError(index, fileIndex)}">
                                            {{ file.name }} ({{ (file.size / 1000).toFixed(1) }} KB)
                                        </span>
                                        <span>
                                            <i class="fas fa-times text-danger cursor-pointer"
                                               @click="removeFile(index, fileIndex)">
                                            </i>
                                        </span>
                                        <small v-if="hasFileError(index, fileIndex)" class="form-text error">{{ getFileError(index, fileIndex) }}</small>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group mt-1">
                            <button class="btn btn-light"
                                    @click="(event) => event.target.parentElement.querySelector('input').click()">
                                Nahrát soubory
                            </button>
                            (Max 2 MB / soubor)
                            <input type="file" class="form-control-file d-none h-0" :id="`question-files-${index}`"
                                   multiple="multiple" @change="(event) => uploadFiles(event, index)">
                        </div>

                        <div class="form-group">
                            <label :for="`question-type-${index}`">Typ otázky</label>
                            <select v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.type`]}"
                                    :id="`question-type-${index}`" v-model="question.type">
                                <option v-for="type in types" :value="type">{{ typeLabels[type] }}</option>
                            </select>
                            <small v-if="!!errors[`questions.${index}.type`]" class="form-text error">{{ errors[`questions.${index}.type`][0] }}</small>
                        </div>

                        <template v-if="question.type !== types.options && question.type !== types.options_checkbox">
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label :for="`question-min-points-${index}`">Minimální počet bodů</label>
                                    <input type="number" :id="`question-min-points-${index}`"
                                           step="0.1"
                                           v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.min_points`]}"
                                           v-model="question.min_points">
                                    <small v-if="!!errors[`questions.${index}.min_points`]" class="form-text error">{{ errors[`questions.${index}.min_points`][0] }}</small>
                                </div>
                                <div class="form-group  col-12 col-md-6">
                                    <label :for="`question-max-points-${index}`">Maximální počet bodů</label>
                                    <input type="number" :id="`question-max-points-${index}`"
                                           step="0.1"
                                           v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.max_points`]}"
                                           v-model="question.max_points">
                                    <small v-if="!!errors[`questions.${index}.max_points`]" class="form-text error">{{ errors[`questions.${index}.max_points`][0] }}</small>
                                </div>
                            </div>
                        </template>

                        <div class="form-group" v-if="question.type === types.files">
                            <label :for="`question-files-${index}`">Maximální počet souborů nahraných studentem</label>
                            <input type="number" :id="`question-files-${index}`"
                                   v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.files_number`]}"
                                   v-model="question.files_number">
                            <small v-if="!!errors[`questions.${index}.files_number`]" class="form-text error">{{ errors[`questions.${index}.files_number`][0] }}</small>
                        </div>

                        <div v-if="question.type === types.options || question.type === types.options_checkbox">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">Možnost</th>
                                    <th scope="col">Počet bodů</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(option, optionIndex) in question.options" :key="`option-${optionIndex}`">
                                    <td>
                                        <input v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.options.${optionIndex}.text`]}"
                                               type="text" v-model="option.text">
                                        <small v-if="!!errors[`questions.${index}.options.${optionIndex}.text`]"
                                               class="form-text error">{{ errors[`questions.${index}.options.${optionIndex}.text`][0] }}</small>
                                    </td>
                                    <td>
                                        <input v-bind:class="{'form-control': true, 'border-danger': !!errors[`questions.${index}.options.${optionIndex}.points`]}"
                                               step="0.1"
                                               type="number" v-model="option.points">
                                        <small v-if="!!errors[`questions.${index}.options.${optionIndex}.points`]"
                                               class="form-text error">{{ errors[`questions.${index}.options.${optionIndex}.points`][0] }}</small>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <i class="fas fa-minus-circle text-danger cursor-pointer h4"
                                           @click="removeOption(index, optionIndex)"></i>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-info" @click="addOption(index)">
                                <i class="fas fa-plus"></i> Přidat možnost
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-info" @click="addQuestion">
            <i class="fas fa-plus"></i> Přidat otázku
        </button>
    </div>
</template>

<script>
export default {
    props: {
        questions: {
            type: Array,
            required: true,
        },

        errors: {
            type: Object,
            required: false,
            default() {
                return {};
            },
        },
    },

    data() {
        return {
            dataQuestions: this.$props.questions,
            types: {
                options: 0,
                options_checkbox: 1,
                line: 2,
                text: 3,
                files: 4,
            },
            typeLabels: [
                'Výběr z možností',
                'Výběr vícero možností',
                'Jednořádková odpověď',
                'Víceřádková odpověď',
                'Nahrání soubru',
            ],
        };
    },

    methods: {
        addQuestion() {
            this.dataQuestions.push({name: '', type: this.types.line, min_points: 0, max_points: 0, files_number: 0,});
        },

        removeQuestion(index) {
            this.$confirm("Naozaj chcete odstránit otázku?", '', null, {
                confirmButtonText: 'Ano',
                cancelButtonText: 'Ne',
            })
                .then(() => {
                    this.dataQuestions.splice(index, 1);
                })
                .catch(() => {
                });
        },

        uploadFiles(event, index) {
            let component = this;
            if (!Array.isArray(component.dataQuestions[index].files)) {
                component.dataQuestions[index].files = [];
            }

            [...event.target.files].forEach(file => {
                let reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = function () {
                    if (file.size > 2000000) {
                        component.$alert(`Soubor ${file.name} je příliš velký`);
                        return;
                    }

                    if (component.dataQuestions[index].files.findIndex(x => x.name === file.name) !== -1) {
                        component.$alert(`Soubor ${file.name} již existuje`);
                        return;
                    }

                    component.dataQuestions[index].files.push({
                        name: file.name,
                        base64: reader.result,
                        size: file.size,
                    });
                    component.$forceUpdate();
                };

                reader.onerror = function () {
                    component.$alert(`Soubor ${file.name} se nepodařilo načíst`);
                };
            });

            event.target.value = null;
        },

        removeFile(questionIndex, fileIndex) {
            this.dataQuestions[questionIndex].files.splice(fileIndex, 1);
            this.$forceUpdate();
        },

        removeOption(questionIndex, optionIndex) {
            this.dataQuestions[questionIndex].options.splice(optionIndex, 1);
            this.$forceUpdate();
        },

        addOption(index) {
            if (!Array.isArray(this.dataQuestions[index].options)) {
                this.dataQuestions[index].options = [];
            }

            this.dataQuestions[index].options.push({text: '', points: 0});
            this.$forceUpdate();
        },

        hasQuestionError(questionIndex) {
            const filter = new RegExp(`^questions\.${questionIndex}\..*$`);
            for (const key in this.errors) {
                if (filter.test(key)) {
                    return true;
                }
            }
            return false;
        },

        hasFileError(questionIndex, fileIndex) {
            return this.errors[`questions.${questionIndex}.files.${fileIndex}.base64`] ||
                this.errors[`questions.${questionIndex}.files.${fileIndex}.name`] ||
                this.errors[`questions.${questionIndex}.files.${fileIndex}.size`]
        },

        getFileError(questionIndex, fileIndex) {
            return [this.errors[`questions.${questionIndex}.files.${fileIndex}.base64`] ||
                this.errors[`questions.${questionIndex}.files.${fileIndex}.name`] ||
                this.errors[`questions.${questionIndex}.files.${fileIndex}.size`]][0]
        },
    },
}
</script>

<style scoped>

</style>
