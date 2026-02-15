<?php

return [
    // Modal Headers
    'create_title' => '反馈',
    'create_subtitle' => '帮助我们改进此系统',
    'edit_title' => '编辑反馈',
    'edit_subtitle' => '更新您的反馈',
    'respond_title' => '回复反馈',
    'respond_subtitle' => '为用户反馈提供回复',

    // Index Page
    'page_subtitle_admin' => '管理所有用户的反馈',
    'page_subtitle_user' => '查看和管理您的反馈',
    'send_new_feedback' => '发送新反馈',
    'send_first_feedback' => '发送第一条反馈',
    'send_feedback' => '发送反馈',

    // Stats
    'stat_in_progress' => '进行中',
    'stat_resolved' => '已解决',
    'stat_bugs' => '缺陷',
    'stat_features' => '功能请求',
    'stat_suggestions' => '建议',

    // Tabs
    'tab_my_feedbacks' => '我的反馈',
    'tab_all_feedbacks' => '所有反馈',

    // Table Headers
    'header_type' => '类型',
    'header_title' => '标题',
    'header_sender' => '发送者',
    'header_priority' => '优先级',
    'header_status' => '状态',
    'header_created' => '创建时间',
    'header_actions' => '操作',

    // Filter
    'search_all' => '搜索标题、描述或发送者姓名...',
    'search_own' => '搜索标题或描述...',
    'filter_type' => '类型',
    'filter_all_types' => '所有类型',
    'filter_priority' => '优先级',

    // Status Options
    'status_open' => '待处理',
    'status_in_progress' => '进行中',
    'status_resolved' => '已解决',
    'status_closed' => '已关闭',

    // Priority Options
    'priority_low' => '低',
    'priority_medium' => '中',
    'priority_high' => '高',
    'priority_critical' => '紧急',

    // Form Labels
    'feedback_type' => '反馈类型',
    'priority' => '优先级',
    'title_placeholder' => '简要概述您的反馈...',
    'description_placeholder' => '请详细说明... 如果是缺陷报告，请包含重现问题的步骤。',
    'page_url' => '页面 URL',
    'page_url_hint' => '与此反馈相关的页面 URL（自动填充）',
    'max_characters' => '最多 :count 个字符',
    'response' => '回复',
    'response_placeholder' => '输入您对此反馈的回复...',
    'response_notification_hint' => '用户将收到有关此回复的通知',

    // Type Options
    'type_bug' => '缺陷报告',
    'type_feature' => '功能请求',
    'type_feedback' => '意见/建议',
    'select_type' => '选择类型...',

    // Priority Options (Select)
    'select_priority' => '选择优先级...',

    // Attachment
    'screenshot_attachment' => '截图 / 附件',
    'attachment_optional' => '可选 - 上传截图或支持文档',
    'paste_hint' => '按 Ctrl+V 粘贴图片',
    'file_label' => '文件',
    'file_tip' => 'JPG、PNG 或 PDF（最大 5MB）',
    'processing_clipboard' => '正在处理剪贴板图片...',
    'file_too_large' => '文件过大',
    'max_image_size' => '图片最大尺寸为 5MB',
    'upload_failed' => '上传失败',
    'upload_error' => '上传图片时发生错误',
    'replace_attachment' => '替换附件',
    'attachment_label' => '附件',
    'click_to_open' => '点击打开',

    // Help Tips
    'tips_title' => '良好反馈的提示：',
    'tip_bug' => '包含重现步骤、预期行为与实际行为',
    'tip_feature' => '说明使用场景和所需功能的好处',
    'tip_feedback' => '提供背景和具体的改进建议',

    // Respond Form
    'from' => '来自',
    'source_page' => '来源页面',
    'respond_info' => '您的回复将直接以通知形式发送给用户，并显示在反馈详情中。',
    'send_response' => '发送回复',
    'select_status' => '选择状态...',

    // Show
    'admin_response' => '管理员回复',
    'respond' => '回复',

    // Empty State
    'no_feedback_yet' => '暂无反馈',
    'no_feedback_admin' => '用户尚未提交任何反馈',
    'no_feedback_user' => '您尚未提交任何反馈',

    // Action Tooltips
    'view_detail' => '查看详情',

    // Delete
    'delete_title' => '删除反馈？',
    'delete_confirm_message' => '反馈":title"将被永久删除。此操作无法撤销。',
    'delete_confirm' => '是的，删除',
    'deleted_success' => '反馈删除成功',
    'no_delete_permission' => '您没有权限删除此反馈',
    'no_view_permission' => '您没有权限访问此反馈',

    // Status Change
    'status_changed' => '状态已更改为 :status',

    // Validation Messages
    'validation' => [
        'title_required' => '标题为必填项',
        'title_max' => '标题不能超过 255 个字符',
        'description_required' => '描述为必填项',
        'description_max' => '描述不能超过 5000 个字符',
        'attachment_max' => '文件大小不能超过 5MB',
        'attachment_mimes' => '文件必须为 JPG、PNG 或 PDF 格式',
        'response_required' => '回复为必填项',
        'response_max' => '回复不能超过 5000 个字符',
    ],

    // Notification
    'notification_new_title' => '收到新反馈',
    'notification_new_message' => '来自 :user 的 :type：:title',
    'notification_responded_title' => '反馈已回复',
    'notification_responded_message' => '您的反馈":title"已由 :responder 回复',

    // Messages
    'created_success' => '反馈提交成功！感谢您的意见。',
    'updated_success' => '反馈更新成功',
    'response_sent' => '回复发送成功',
    'not_found' => '未找到反馈',
    'no_edit_permission' => '您没有权限编辑此反馈',
    'cannot_edit_processed' => '已处理的反馈无法编辑',
    'cannot_edit' => '反馈无法编辑',
    'no_respond_permission' => '您没有权限回复反馈',
    'cannot_respond' => '此反馈无法回复',
];
