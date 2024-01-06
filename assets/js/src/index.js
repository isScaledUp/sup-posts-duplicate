import {registerPlugin} from "@wordpress/plugins";
import {DuplicateDashboardButton} from "./DuplicateDashboardButton";

registerPlugin('sup-post-duplicate', {
	render: DuplicateDashboardButton,
	icon: 'admin-page',
});
