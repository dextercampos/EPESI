<?php
declare(strict_types=1);

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
trait Custom_MailChimp_Traits_MergeFieldCommon
{
    private static $EXLUDED_FIELDS = [
        'f_skip_date',
        'f_login',
        'f_record_manager'
    ];

    public static function get_fields(
        string $recordset,
        $additionalExcludes = null,
        $prefix = null,
        $prettyPrint = null,
        $sort = null,
        $optionize = null
    ) {
        // Defaults
        $additionalExcludes = $additionalExcludes ?? [];
        $prefix = $prefix ?? '';
        $prettyPrint = $prettyPrint ?? true;
        $sort = $sort ?? false;
        $optionize = $optionize ?? false;

        $mergeFields = [];

        // Fails on an empty recordset
        $recordInfo = DB::GetCol(sprintf(
            "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='%s' AND `TABLE_NAME`='%s_data_1';",
            DATABASE_NAME,
            $recordset
        ));

        $fieldNames = DB::GetCol(sprintf(
            "SELECT field FROM %s_field WHERE active=1 AND type NOT IN ('foreign index', 'page_split','multiselect','calculated') AND field NOT IN ('Permission')",
            $recordset
        ));

        if ($sort) {
            sort($fieldNames);
        }

        foreach ($fieldNames as $field) {
            // Replace spaces with underscores
            $mergeFieldKey = strtolower(str_replace(' ', '_', $field));
            // Prefix f_ to match database fields
            $databaseField = 'f_' . $mergeFieldKey;
            $boolIncludeField = \in_array($databaseField, $recordInfo) === true &&
                \in_array($databaseField, self::$EXLUDED_FIELDS) === false &&
                \in_array($mergeFieldKey, $additionalExcludes) === false;

            // Add prefix to key if present
            $mergeFieldKeyPrefixed = ($prefix ? $prefix . '_' : '') . $mergeFieldKey;

            if ($boolIncludeField && $prettyPrint) {
                $mergeFieldValue = ucwords(str_replace('_', ' ', $mergeFieldKey));
                $mergeFields[$mergeFieldKeyPrefixed] = $mergeFieldValue;
                continue;
            }

            if ($boolIncludeField && !$prettyPrint) {
                $mergeFields[] = $mergeFieldKey;
            }
        }

        if ($optionize) {
            $optionText = '';
            foreach ($mergeFields as $key => $value) {
                $optionText .= sprintf("<option value='%s'>%s</option>", $key, $value);
            }

            return $optionText;
        }

        return $mergeFields && !empty($mergeFields) ? $mergeFields : false;
    }

    public static function merge_field_addon_label($record, $rb_obj)
    {
        return ['show' => true, 'label' => __('MailChimp Contact Merge Fields')];
    }
}
