import SubIdxLanguages from "./components/SubIdxLanguages";
import FileGroupJobs from "./components/FileGroupJobs";
import SupJob from "./components/SupJob";
import FileGroupArchive from "./components/FileGroupArchive";
import DownloadLink from "./components/helpers/DownloadLink";

require('./bootstrap');

window.Vue = require('vue');

Vue.config.productionTip = false;
Vue.config.devtools = false;

Vue.component('sub-idx-languages', SubIdxLanguages);
Vue.component('file-group-jobs', FileGroupJobs);
Vue.component('sup-job', SupJob);
Vue.component('file-group-archive', FileGroupArchive);
Vue.component('download-link', DownloadLink);

const app = new Vue({
    el: '#app'
});

require('../react/sub-idx-batch-result');
