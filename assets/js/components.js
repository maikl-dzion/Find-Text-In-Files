
Vue.component('tree-view-template', {

    props: ['name', 'item'],
    template: '#tree-view-template',

    data() {
        let path = (this.item['path']) ? this.item['path'] : '';
        let type = (this.item['type']) ? this.item['type'] : 'dir';
        return {
            type,
            path,
            childList: {},
            openChildDir: false,
        }
    },

    methods: {
        getChildDir() {
            let path = this.path;
            this.createEvent('set-path', { path });
            this.openChildDir = (this.openChildDir) ? false : true;
            if (this.type == 'dir') {
                if (Object.keys(this.childList).length == 0) {
                    this.post('/scan/dir/child', { path }, this.setChildList);
                }
            }
        },

        setChildList(response) {
            this.childList = {};
            this.childList = response['result'];
        },

    },

});


Vue.component('find-result-view', {
    props: ['list', 'path'],
    template: '#find-result-view-template',
});
