<?php

return [
    // Invoice Header
    'invoice' => '发票',
    'invoice_number' => '发票号',
    'invoice_date' => '发票日期',
    'due_date' => '到期日',
    'bill_to' => '账单收件人',
    'from' => '发件人',

    // Company Info
    'company_name' => '公司名称',
    'company_address' => '公司地址',
    'company_phone' => '电话',
    'company_email' => '邮箱',
    'npwp' => '税号',

    // Client Info
    'client_name' => '客户名称',
    'client_address' => '地址',
    'client_phone' => '电话',
    'client_email' => '邮箱',

    // Table Headers
    'no' => '序号',
    'item_description' => '项目描述',
    'description' => '描述',
    'qty' => '数量',
    'quantity' => '数量',
    'unit' => '单位',
    'unit_price' => '单价',
    'price' => '价格',
    'discount' => '折扣',
    'amount' => '金额',
    'total' => '总计',

    // Units
    'pcs' => '件',
    'unit_pcs' => '件',
    'unit_m3' => '立方米',
    'unit_kg' => '千克',
    'unit_ton' => '吨',
    'unit_liter' => '升',
    'unit_box' => '箱',
    'unit_pack' => '包',

    // Summary
    'subtotal' => '小计',
    'tax' => '税',
    'ppn' => '增值税',
    'pph' => '预扣税',
    'pph_22' => '预扣税22',
    'pph_23' => '预扣税23',
    'down_payment' => '首付款',
    'dp' => '首付',
    'payment' => '付款',
    'grand_total' => '总计',
    'total_amount' => '总金额',
    'total_invoice' => '发票总额',
    'amount_paid' => '已付金额',
    'faktur' => '发票',
    'amount_remaining' => '剩余金额',
    'balance_due' => '应付余额',

    // Payment Info
    'payment_terms' => '付款条款',
    'payment_method' => '付款方式',
    'payment_to' => '付款至',
    'bank_account' => '银行账户',
    'bank_name' => '银行名称',
    'account_number' => '账号',
    'account_holder' => '账户持有人',
    'tax_deposit' => '税金寄存',

    // Notes & Terms
    'notes' => '备注',
    'terms_conditions' => '条款与条件',
    'payment_instructions' => '付款说明',
    'thank_you' => '感谢您的信任',
    'thank_you_message' => '感谢您的业务',

    // Signature
    'authorized_signature' => '授权签名',
    'signature' => '签名',
    'stamp' => '印章',
    'approved_by' => '批准人',
    'prepared_by' => '制作人',

    // Number in Words
    'say' => '大写',
    'rupiah' => '印尼盾',

    // Status
    'draft' => '草稿',
    'sent' => '已发送',
    'paid' => '已付款',
    'unpaid' => '未付款',
    'partially_paid' => '部分付款',
    'overdue' => '逾期',

    // Actions
    'download_pdf' => '下载PDF',
    'print_invoice' => '打印发票',
    'send_invoice' => '发送发票',
    'view_invoice' => '查看发票',
    'edit_invoice' => '编辑发票',
    'delete_invoice' => '删除发票',
    'create_invoice' => '创建发票',

    // Types
    'down_payment_invoice' => '首付款发票',
    'settlement_invoice' => '结算发票',
    'full_payment_invoice' => '全额付款发票',
    'settlement' => '结算',

    // Additional Labels
    'client' => '客户',
    'invoice_total' => '发票总额',
    'down_payment_paid' => '已付首付款',
    'remaining_payment' => '剩余付款',
    'already_paid' => '已支付',
    'settlement_amount' => '结算金额',
    'service_subtotal' => '服务小计',
    'dpp' => '应税所得',
    'pp_55' => 'PP 55 (0.5%)',
    'total_down_payment' => '首付款总额',
    'total_settlement' => '结算总额',

    // Notes
    'multiple_clients_note' => '此发票包含 :count 个客户的账单',
    'includes_tax_deposit' => '包括税金寄存',
    'down_payment_info' => '首付款信息',
    'settlement_info' => '结算信息',
    'final_settlement_note' => '这是最终结算发票',
    'pph_final_note' => '根据 PP No. 23/2018 中小企业最终预扣税 0.5%',
    'tax_deposit_excluded_note' => '税金寄存不包含在最终预扣税计算中',
    'period' => '期间',
    'please_remit_to' => '请汇款至',
    'labor_cost' => '人工成本',
    'operational_cost' => '运营成本',
    'invoice_details' => '发票详情',
    'payment_to_account' => '付款至账户',
    'payment_before_due_date' => '请在到期日前付款。',

    // Faktur upload
    'faktur_upload' => '发票（PDF/图片）',
    'file_selected_success' => '文件选择成功',
    'upload_instructions' => '点击上传或拖放',
    'faktur_file_types' => 'PDF, JPG, JPEG, PNG（最大5MB）',
    'filename_optional' => '文件名（可选）',
    'filename_example' => '例如：发票-',
    'filename_note' => '留空以使用原始文件名。扩展名将自动添加。',
    'new_file_selected' => '新文件已选择',
    'new_file_replace_note' => '上传新文件以替换当前发票',

    // Messages
    'no_items' => '无项目',
    'invoice_created' => '发票创建成功',
    'invoice_updated' => '发票更新成功',
    'invoice_deleted' => '发票删除成功',
    'unit_price_required' => '必须填写单价。',
    'quantity_required' => '必须填写数量。',
    'created_successfully' => '发票创建成功！',
    'updated_successfully' => '发票更新成功！',
    'creation_failed' => '发票创建失败。',
    'update_failed' => '发票更新失败。',
    'not_found' => '未找到发票',
    'delete_success' => '发票删除成功',
    'delete_error' => '删除发票失败',
    'deletion_success' => '发票删除成功',

    // Delete confirmation
    'delete_confirm_title' => '删除发票？',
    'confirm_delete' => '是的，删除发票',
    'delete_permanent_note' => '将被永久删除。',
    'delete_confirm_message' => '给 :client_name 的发票，金额 Rp :total_amount，包含 :items_count 项',
    'delete_confirm_with_payments' => '和 :payments_count 笔付款（Rp :total_paid）',

    // Send & Print
    'only_draft_can_send' => '只能发送草稿发票',
    'send_success' => '发票 :invoice_number 发送成功',
    'send_error' => '发送发票失败',
    'select_for_print' => '请至少选择一张发票打印',
    'download_started' => '正在下载 :count 张发票PDF',
    'download_error' => '下载启动失败',

    // Bulk actions
    'select_for_delete' => '请至少选择一张发票删除',
    'bulk_delete_success' => '成功删除 :count 张发票',
    'only_sent_rollback' => '只能将已发送的发票回退到草稿',
    'rollback_success' => '发票 :invoice_number 已回退到草稿',
    'rollback_error' => '回退失败',
    'invalid_dp_amount' => 'DP金额无效',
    'already_paid_full' => '发票已全额支付',

    // Templates
    'template_kisantra' => 'Kisantra',
    'template_kisantra_desc' => '默认模板',
    'template_semesta' => 'Semesta',
    'template_semesta_desc' => '矿业（含PPN + PPH 22）',
    'template_agsa' => 'AGSA',
    'template_agsa_desc' => '备选',
    'template_generic' => 'Generic',
    'template_generic_desc' => '简单',
];
