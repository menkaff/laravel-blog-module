const notfound = {
    template: '<div>404</div>'
}
const main = {
    template: `
  <router-view></router-view>
  `,
}
const index = {
    template: `
    <div class="col-md-6">
    <h3>
    <router-link class="btn btn-info"  :to="'/blog/post?id='+post.id">
    {{ post.title }}
    </router-link>   
    </h3>
    </div>
    `,
    data() {
        return {
            loading: false,
            posts: [],
            error: null,
        }
    },
    created() {
        this.fetchData();
    },
    //watch: {
    // call again the method if the route changes
    //'$route': 'fetchData'
    //},
    methods: {
        fetchData() {
            this.error = this.post = null
            this.loading = true
            console.log(window.Laravel.csrfToken);
            // replace `getPost` with your data fetching util / API wrapper
            axios.get('/api/blog/post', {
                headers: {
                    'Accept': 'application/json',
                    //'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImMyOTJmMzAxYTkyZmQ0ZGFmNTNhMGNlMGI5YTEzN2FmZWU4MDQwZmVlYTg4ZGM3N2EzY2Q2NmMxNTg5ODcxMmI1NTRhNTRlYzU4M2ZiZTViIn0.eyJhdWQiOiI0IiwianRpIjoiYzI5MmYzMDFhOTJmZDRkYWY1M2EwY2UwYjlhMTM3YWZlZTgwNDBmZWVhODhkYzc3YTNjZDY2YzE1ODk4NzEyYjU1NGE1NGVjNTgzZmJlNWIiLCJpYXQiOjE1MDUzNzMxNDgsIm5iZiI6MTUwNTM3MzE0OCwiZXhwIjoxNTM2OTA5MTQ4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.Fhrx1hsnkuYMkoZFU5jN8V_Al_rZPJSDbBbr0xTYhlCW7VCAsg7bpq33LYIQiIq4Zv8TvJa4CZxHTNCx_VW1kmfVBQWaLiq7zVchr3_L5MzhcEH-mXbg1ZimMJHqe54p5KmhLLa-1H581h9NOiKcm0iEy6PyjEALJKxgETXirVKveriSNP7483OUYV-G-cdyeTCrR1sRnslljV3eOwkBBX57PdFO2uj3-KaD5MHEaatpIFpXkCNnjh9oI-GM6bE2pVm1p-rP86lPacetrtW7Ji9zn92hHPG21HzoZtxCdm4fbXssakHkC3iSF0onyR2vvJ4cfNy_8PCO800lCwHo0hqGq7Xfqb4UbK9aFfF6m0zxd8hOePCLvIy2zAFL10S3eaVzKjktjK1OqXiN7e9kzvgZ5Ce2k3LR8qT9u4ATl_i2RqQmn-sle7HA8eQLjAFjOvfuE3eIqIh6XXkObcgYSIHF9fjkd68nrWh5hmjsX1VylGIT58KVbSJOYmJRVjZLcT5IPfVwHxuQvS2vdmDZBEyIKcN9yM6YBNs0HY2I6iQaeRIqKDcUmYkgrtFGbrobna-OLhQj1lv28z7fp4v_mwmmMnWxRUalzojjeOrljahlHHFoK1zVJimJeEtkTrqyhNAphpnZdSl1GqSTU_BPk__oa-bC0XdV5zFn8T27wh0',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).then(response => {
                this.loading = false;
                this.posts = response.data;
                console.log(response.data);
            }).catch(function(error) {
                console.log(error);
            });
        }
    }
}
const router = new VueRouter({
    mode: 'history',
    routes: [{
        path: '/blog/post',
        component: main,
        children: [{
            // UserProfile will be rendered inside User's <router-view>
            // when /user/:id/profile is matched
            path: '',
            component: index,
            beforeEnter: (to, from, next) => {
                // ...
                next();
            }
        }, {
            // UserPosts will be rendered inside User's <router-view>
            // when /user/:id/posts is matched
            path: 'show',
            component: notfound
        }]
    }, {
        path: '*',
        component: notfound
    }, ],
    scrollBehavior(to, from, savedPosition) {
        return {
            x: 1000,
            y: 1000
        }
    }
})
const app = new Vue({
    router
}).$mount('#app')