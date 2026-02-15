<?php

return [
    // Modal Headers
    'create_title' => 'Feedback',
    'create_subtitle' => 'Help us improve this system',
    'edit_title' => 'Edit Feedback',
    'edit_subtitle' => 'Update your feedback',
    'respond_title' => 'Respond to Feedback',
    'respond_subtitle' => 'Provide a response for user feedback',

    // Index Page
    'page_subtitle_admin' => 'Manage feedback from all users',
    'page_subtitle_user' => 'View and manage your feedback',
    'send_new_feedback' => 'Send New Feedback',
    'send_first_feedback' => 'Send First Feedback',
    'send_feedback' => 'Send Feedback',

    // Stats
    'stat_in_progress' => 'In Progress',
    'stat_resolved' => 'Resolved',
    'stat_bugs' => 'Bugs',
    'stat_features' => 'Features',
    'stat_suggestions' => 'Suggestions',

    // Tabs
    'tab_my_feedbacks' => 'My Feedbacks',
    'tab_all_feedbacks' => 'All Feedbacks',

    // Table Headers
    'header_type' => 'Type',
    'header_title' => 'Title',
    'header_sender' => 'Sender',
    'header_priority' => 'Priority',
    'header_status' => 'Status',
    'header_created' => 'Created',
    'header_actions' => 'Actions',

    // Filter
    'search_all' => 'Search title, description, or sender name...',
    'search_own' => 'Search title or description...',
    'filter_type' => 'Type',
    'filter_all_types' => 'All Types',
    'filter_priority' => 'Priority',

    // Status Options
    'status_open' => 'Open',
    'status_in_progress' => 'In Progress',
    'status_resolved' => 'Resolved',
    'status_closed' => 'Closed',

    // Priority Options
    'priority_low' => 'Low',
    'priority_medium' => 'Medium',
    'priority_high' => 'High',
    'priority_critical' => 'Critical',

    // Form Labels
    'feedback_type' => 'Feedback Type',
    'priority' => 'Priority',
    'title_placeholder' => 'Brief summary of your feedback...',
    'description_placeholder' => 'Explain in detail... For bug reports, include steps to reproduce the issue.',
    'page_url' => 'Page URL',
    'page_url_hint' => 'URL of the page related to this feedback (auto-filled)',
    'max_characters' => 'Maximum :count characters',
    'response' => 'Response',
    'response_placeholder' => 'Write your response for this feedback...',
    'response_notification_hint' => 'The user will receive a notification about this response',

    // Type Options
    'type_bug' => 'Bug Report',
    'type_feature' => 'Feature Request',
    'type_feedback' => 'Feedback',
    'select_type' => 'Select type...',

    // Priority Options (Select)
    'select_priority' => 'Select priority...',

    // Attachment
    'screenshot_attachment' => 'Screenshot / Attachment',
    'attachment_optional' => 'Optional - Upload a screenshot or supporting document',
    'paste_hint' => 'Press Ctrl+V to paste image',
    'file_label' => 'File',
    'file_tip' => 'JPG, PNG, or PDF (Max 5MB)',
    'processing_clipboard' => 'Processing image from clipboard...',
    'file_too_large' => 'File too large',
    'max_image_size' => 'Maximum image size is 5MB',
    'upload_failed' => 'Upload failed',
    'upload_error' => 'An error occurred while uploading the image',
    'replace_attachment' => 'Replace Attachment',
    'attachment_label' => 'Attachment',
    'click_to_open' => 'Click to open',

    // Help Tips
    'tips_title' => 'Tips for good feedback:',
    'tip_bug' => 'Include steps to reproduce, expected vs actual behavior',
    'tip_feature' => 'Explain the use case and benefits of the desired feature',
    'tip_feedback' => 'Provide context and concrete suggestions for improvement',

    // Respond Form
    'from' => 'From',
    'source_page' => 'Source Page',
    'respond_info' => 'Your response will be sent directly to the user as a notification and displayed in the feedback details.',
    'send_response' => 'Send Response',
    'select_status' => 'Select status...',

    // Show
    'admin_response' => 'Admin Response',
    'respond' => 'Respond',

    // Empty State
    'no_feedback_yet' => 'No feedback yet',
    'no_feedback_admin' => 'No feedback has been submitted by users',
    'no_feedback_user' => 'You have not submitted any feedback yet',

    // Action Tooltips
    'view_detail' => 'View Detail',

    // Delete
    'delete_title' => 'Delete Feedback?',
    'delete_confirm_message' => 'Feedback ":title" will be permanently deleted. This action cannot be undone.',
    'delete_confirm' => 'Yes, Delete',
    'deleted_success' => 'Feedback deleted successfully',
    'no_delete_permission' => 'You do not have permission to delete this feedback',
    'no_view_permission' => 'You do not have permission to access this feedback',

    // Status Change
    'status_changed' => 'Status changed to :status',

    // Validation Messages
    'validation' => [
        'title_required' => 'Title is required',
        'title_max' => 'Title must not exceed 255 characters',
        'description_required' => 'Description is required',
        'description_max' => 'Description must not exceed 5000 characters',
        'attachment_max' => 'File size must not exceed 5MB',
        'attachment_mimes' => 'File must be JPG, PNG, or PDF format',
        'response_required' => 'Response is required',
        'response_max' => 'Response must not exceed 5000 characters',
    ],

    // Notification
    'notification_new_title' => 'New Feedback Received',
    'notification_new_message' => ':type from :user: :title',
    'notification_responded_title' => 'Feedback Responded',
    'notification_responded_message' => 'Your feedback ":title" has been responded to by :responder',

    // Messages
    'created_success' => 'Feedback submitted successfully! Thank you for your input.',
    'updated_success' => 'Feedback updated successfully',
    'response_sent' => 'Response sent successfully',
    'not_found' => 'Feedback not found',
    'no_edit_permission' => 'You do not have permission to edit this feedback',
    'cannot_edit_processed' => 'Processed feedback cannot be edited',
    'cannot_edit' => 'Feedback cannot be edited',
    'no_respond_permission' => 'You do not have permission to respond to feedback',
    'cannot_respond' => 'This feedback cannot be responded to',
];
