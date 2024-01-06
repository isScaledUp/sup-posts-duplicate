import {__} from '@wordpress/i18n';
import {PluginMoreMenuItem, PluginPostStatusInfo} from '@wordpress/edit-post';
import {copy as Copy} from '@wordpress/icons';
import {useEffect, useState} from "react";
import apiFetch from '@wordpress/api-fetch';
import {Spinner} from '@wordpress/components';


export const DuplicateDashboardButton = () => {

	const [postId, setPostId] = useState();
	const [loading, setLoading] = useState(false);

	useEffect(() => {
		setPostId(wp.data.select('core/editor').getCurrentPostId());
	}, []);


	async function onDuplicateClick() {
		if (loading) return;

		if (!postId) return alert(__('Error GE-001: Could not get post ID', 'sup-post-duplicate'));
		setLoading(true);
		// Redirect to the new post
		const response = await apiFetch({
			path: 'sup-posts-duplicate/v1/duplicate',
			method: 'POST',
			// Don't parse the response as JSON
			parse: false,
			// Pass the post ID to the endpoint
			data: {
				post_id: postId
			}
		});

		if (response.status === 200) {
			const location = response.headers.get('X-Location');
			location && window.open(location, '_blank');
		}
		setLoading(false);
	}

	const buttonStyle = {
		opacity: loading ? .5 : 1,
		pointerEvents: loading ? 'none' : 'all',
		display: 'inline-flex',
		alignItems: 'center',
		gap: '5px'
	}

	return (
		<PluginPostStatusInfo>
			<button style={buttonStyle}
					onClick={onDuplicateClick}
					className='components-button is-link'>
				{__('Duplicate Post', 'sup-post-duplicate')}
				{loading && <Spinner/>}
			</button>
		</PluginPostStatusInfo>
	);

}
