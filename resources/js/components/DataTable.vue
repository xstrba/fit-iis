<template>
    <div>
        <div class="row mb-2">
            <div class="col-lg-8">
                <div class="row">
                    <div v-for="(filter, index) in filters" class="col-12 col-md-6 col-xl-4" :key="`filter-${index}`">
                        <div class="form-group mb-0">
                            <label :for="`filterSelect-${filter.key}`">{{ filter.label }}</label>
                            <select class="form-control"
                                    :id="`filterSelect-${filter.key}`"
                                    :multiple="filter.multiple"
                                    v-model="selectedFilters[filter.key]"
                                    @change="filtersChanged">
                                <option v-for="option in filter.options" :value="option.value">{{
                                        option.label
                                    }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-4">
                        <label></label>
                        <form @submit.prevent="search">
                            <label class="sr-only" for="searchInput">Hledat</label>
                            <div class="input-group mr-sm-2">
                                <input type="text"
                                       class="form-control"
                                       id="searchInput"
                                       placeholder="Hledej..."
                                       v-model="searchValue"
                                       autocomplete="off"
                                       autocapitalize="off">
                                <div class="input-group-prepend">
                                    <div class="input-group-text cursor-pointer" @click="search"><i
                                        class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <vuetable ref="vuetable"
                  :api-url="dataApiUrl"
                  :fields="fields"
                  data-path="data"
                  pagination-path=""
                  :css="css.table"
                  :multi-sort="true"
                  multi-sort-key="ctrl"
                  @vuetable:pagination-data="onPaginationData">
            <template v-for="field in fields"
                 v-if="field.name !== 'actions'"
                 :slot="field.name"
                 slot-scope="props">
                <slot :name="field.name" v-bind:rowData="props.rowData">
                    <span class="text-primary">{{ props.rowData[field.name] }}</span>
                </slot>
            </template>
            <div slot="actions" slot-scope="props">
                <div class="btn-group">
                    <button type="button"
                            class="btn btn-outline-info"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <template v-for="(action, index) in props.rowData.actions">
                            <a v-if="!action.method" :href="action.action" class="dropdown-item">
                                <i :class="`fas fa-${action.icon} mr-2`"></i>{{ action.title }}
                            </a>
                            <a v-else-if="action.method === 'post'"
                               href=""
                               class="dropdown-item"
                               @click.prevent="postAction(action.action)">
                                <i :class="`fas fa-${action.icon} mr-2`"></i>{{ action.title }}
                            </a>
                            <a v-else-if="action.method === 'delete'"
                               href=""
                               class="dropdown-item text-danger"
                               @click.prevent="deleteItem(action.action)">
                                <i :class="`fas fa-${action.icon} mr-2`"></i>{{ action.title }}
                            </a>
                            <div v-if="index < props.rowData.actions - 1" class="dropdown-divider"></div>
                        </template>
                    </div>
                </div>
            </div>
        </vuetable>

        <div class="mt-3">
            <vuetable-bootstrap-pagination ref="pagination"
                                           :css="css.pagination"
                                           @vuetable-pagination:change-page="onChangePage"
                                           class="pull-right">
            </vuetable-bootstrap-pagination>
        </div>
    </div>
</template>

<script>
import Vuetable from 'vuetable-2';
import VuetableBootstrapPagination from "./VuetableBootstrapPagination";

export default {
    data() {
        return {
            css: {
                table: {
                    tableWrapper: 'table-responsive',
                    tableHeaderClass: 'mb-0 thead-dark',
                    tableBodyClass: 'mb-0',
                    tableClass: 'table table-striped table-hover',
                    loadingClass: 'loading',
                    ascendingIcon: 'fa fa-chevron-up',
                    descendingIcon: 'fa fa-chevron-down',
                    ascendingClass: 'sorted-asc',
                    descendingClass: 'sorted-desc',
                    sortableIcon: 'fa fa-sort',
                    detailRowClass: 'vuetable-detail-row',
                    handleIcon: 'fa fa-bars text-secondary',
                    renderIcon(classes, options) {
                        return `<i class="${classes.join(' ')}"></span>`
                    }
                },

                pagination: {
                    wrapperClass: 'pagination float-right',
                    activeClass: 'active',
                    disabledClass: 'disabled',
                    pageClass: 'page-item cursor-pointer',
                    linkClass: 'page-link',
                    paginationClass: 'pagination',
                    paginationInfoClass: 'float-left',
                    dropdownClass: 'form-control',
                    icons: {
                        first: 'fas fa-angle-double-left',
                        prev: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        last: 'fas fa-angle-double-right',
                    }
                },
            },

            searchValue: null,
            dataApiUrl: this.$props.apiUrl,
            filters: [],
            selectedFilters: {},
        };
    },

    props: {
        apiUrl: {
            type: String,
            required: true,
        },

        filtersUrl: {
            type: [String, null],
            required: false,
            default: null,
        },

        fields: {
            type: Array,
            required: true,
        },
    },

    components: {
        Vuetable,
        VuetableBootstrapPagination,
    },

    created() {
        if (this.$props.filtersUrl) {
            axios.post(this.$props.filtersUrl).then((response) => {
                this.filters = response.data;
                this.filters.forEach((filter) => {
                    this.selectedFilters[filter.key] = filter.multiple ? [] : filter.options[0].value;
                });
            });
        }
    },

    methods: {
        // when the pagination data is available, set it to pagination component
        onPaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
        },

        // when the user click something that causes the page to change,
        // call "changePage" method in Vuetable, so that that page will be
        // requested from the API endpoint.
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
            this.getFilters();
        },

        search() {
            this.setApiUrl();
            this.onChangePage(1);
        },

        reloadData() {
            this.$refs.vuetable.reload();
            this.getFilters();
        },

        deleteItem(url) {
            axios.delete(url)
                .then(() => {
                    this.reloadData();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },

        postAction(url) {
            axios.post(url)
                .then(() => {
                    this.reloadData();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },

        setApiUrl() {
            let searchString = '';
            if (this.searchValue) {
                searchString = 'search=' + this.searchValue;
            }

            let filters = [];
            Object.keys(this.selectedFilters).forEach((filterKey) => {
                let value = null;
                if ((this.selectedFilters[filterKey] instanceof Array) && this.selectedFilters[filterKey].length) {
                    value = this.selectedFilters[filterKey].join('|');
                } else {
                    value = this.selectedFilters[filterKey];
                }

                if (value !== null) {
                    filters.push(`${filterKey}=${value}`);
                }
            });

            if (searchString) {
                filters.push(searchString);
            }

            let queryString = '';
            if (filters.length) {
                queryString = filters.join('&');
            }

            this.dataApiUrl = queryString ? this.apiUrl + '?' + queryString : this.apiUrl;
        },

        filtersChanged() {
            this.setApiUrl();
            this.onChangePage(1);
        },

        getFilters() {
            if (this.$props.filtersUrl) {
                axios.post(this.$props.filtersUrl).then((response) => {
                    this.filters = response.data;
                    this.filters.forEach((filter) => {
                        if (this.selectedFilters[filter.key] === undefined) {
                            this.selectedFilters[filter.key] = filter.multiple ? [] : filter.options[0].value;
                        } else if (!(this.selectedFilters[filter.key] instanceof Array)) {
                            const optionIndex = filter.options.findIndex(x => x.value === this.selectedFilters[filter.key]);
                            if (optionIndex < 0) {
                                this.selectedFilters[filter.key] = filter.multiple ? [] : filter.options[0].value;
                                this.filtersChanged();
                            }
                        }
                    });
                });
            }
        },
    },
}
</script>

<style scoped>

</style>
