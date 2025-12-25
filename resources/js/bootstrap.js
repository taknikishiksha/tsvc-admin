
import axios from 'axios';

window._ = _;

/**
 * axios global config
 */

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
