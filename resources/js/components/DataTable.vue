<template>
    <div>
        <vuetable ref="vuetable"
                  :api-url="apiUrl"
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
            }
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
        }
    },
}
</script>

<style scoped>

</style>
