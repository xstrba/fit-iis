<template>
    <div>
        <div class="row">
            <div class="offset-md-8 col-md-4">
                <form @submit.prevent="search">
                    <label class="sr-only" for="searchInput">Hledat</label>
                    <div class="input-group mb-2 mr-sm-2">
                        <input type="text"
                               class="form-control"
                               id="searchInput"
                               placeholder="Hledej..."
                               v-model="searchValue">
                        <div class="input-group-prepend">
                            <div class="input-group-text cursor-pointer" @click="search"><i class="fas fa-search"></i></div>
                        </div>
                    </div>
                </form>
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
                    tableWrapper: 'table-responsive-lg',
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
        };
    },

    props: {
        apiUrl: {
            type: String,
            required: true,
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

    methods: {
        // when the pagination data is available, set it to pagination component
        onPaginationData (paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
        },

        // when the user click something that causes the page to change,
        // call "changePage" method in Vuetable, so that that page will be
        // requested from the API endpoint.
        onChangePage (page) {
            this.$refs.vuetable.changePage(page)
        },

        search() {
            let searchString = '';
            if (this.searchValue) {
                searchString = '?search=' + this.searchValue;
            }
            this.dataApiUrl = this.apiUrl + searchString;
            this.onChangePage(1);
            this.reloadData();
        },

        reloadData() {
            this.$refs.vuetable.reload();
        },
    },
}
</script>

<style scoped>

</style>
