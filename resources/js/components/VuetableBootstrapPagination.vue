<template>
    <nav>
        <ul class="pagination float-right">
            <li :class="['page-item', {'cursor-pointer': !isOnFirstPage}, {'disabled': isOnFirstPage}]">
                <a class="page-link" @click.prevent="loadPage('prev')">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            <template v-if="notEnoughPages">
                <li v-for="n in totalPage" :key="n"
                    :class="['page-item', {'cursor-pointer': !isCurrentPage(n)}, {'active': isCurrentPage(n)}]">
                    <a class="page-link" @click.prevent="loadPage(n)" v-html="n"></a>
                </li>
            </template>
            <template v-else>
                <li v-for="n in windowSize" :key="n"
                    :class="['page-item', {'cursor-pointer': !isCurrentPage(windowStart+n-1)}, {'active': isCurrentPage(windowStart+n-1)}]">
                    <a class="page-link" @click.prevent="loadPage(windowStart+n-1)" v-html="windowStart+n-1"></a>
                </li>
            </template>

            <li :class="['page-item', {'cursor-pointer': !isOnLastPage}, {'disabled': isOnLastPage}]">
                <a class="page-link" href="" @click.prevent="loadPage('next')">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
import VuetablePaginationMixin from "vuetable-2/src/components/VuetablePaginationMixin";

export default {
    mixins: [VuetablePaginationMixin]
};
</script>
