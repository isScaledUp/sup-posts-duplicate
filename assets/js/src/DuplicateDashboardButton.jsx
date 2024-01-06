import {__} from '@wordpress/i18n';
import {PluginMoreMenuItem} from '@wordpress/edit-post';
import {copy} from '@wordpress/icons';
import {useEffect, useState} from "react";
import apiFetch from '@wordpress/api-fetch';
import {Spinner} from '@wordpress/components';


export const DuplicateDashboardButton = () => {

	const [postId, setPostId] = useState();
	const [loading, setLoading] = useState(false);

	useEffect(() => {
		setPostId(wp.data.select('core/editor').getCurrentPostId());
	}, []);


	async function onDuplicateClick(e) {
		e.preventDefault();
		e.stopPropagation();
		if (loading) return;

		if (!postId) return alert(__('Error GE-001: Could not get post ID', 'sup-post-duplicate'));
		setLoading(true);
		// Redirect to the new post
		const response = await apiFetch({
			path: 'sup-posts-duplicate/v1/duplicate',
			method: 'POST',
			// Accept redirect
			// Don't parse the response as JSON
			parse: false,
			// Pass the post ID to the endpoint
			data: {
				post_id: postId
			}
		});

		if (response.status === 200) {
			const location = response.headers.get('X-Location');
			console.log(response.headers)
			location && window.open(location, '_blank');
		}
		setLoading(false);
	}

	return (
		<PluginMoreMenuItem icon={copy} onClick={onDuplicateClick}>
			<span style={{opacity: loading ? .5 : 1}}>
				{__('Duplicate Post', 'sup-post-duplicate')}
			</span>
			{loading && <Spinner/>}
		</PluginMoreMenuItem>
	);

}
