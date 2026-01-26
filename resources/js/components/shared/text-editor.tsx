import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import JoditEditor from 'jodit-react';
import { useCallback, useMemo, useRef } from 'react';

interface TextEditorProps {
    label?: string;
    error?: string;
    helperText?: string;
    required?: boolean;
    value?: string;
    onChange?: (content: string) => void;
    onBlur?: (content: string) => void;
    showPreview?: boolean;
    placeholder?: string;
    disabled?: boolean;
    height?: number;
    containerClassName?: string;
    id?: string;
}

const TextEditor = ({
    label,
    error,
    helperText,
    required,
    value = '',
    onChange,
    onBlur,
    showPreview = false,
    placeholder = 'Start typing...',
    disabled = false,
    height = 400,
    containerClassName,
    id,
}: TextEditorProps) => {
    const editor = useRef(null);

    const config = useMemo(
        () => ({
            readonly: disabled,
            placeholder: placeholder,
            buttons: [
                'bold',
                'italic',
                'underline',
                '|',
                'ul',
                'ol',
                '|',
                'font',
                'fontsize',
                'brush',
                '|',
                'image',
                'link',
                '|',
                'align',
                'undo',
                'redo',
            ],
            height: height,
            uploader: {
                insertImageAsBase64URI: true,
            },
        }),
        [disabled, placeholder, height],
    );

    const handleBlur = useCallback(
        (newContent: string) => {
            if (onBlur) {
                onBlur(newContent);
            }
        },
        [onBlur],
    );

    const handleChange = useCallback(
        (newContent: string) => {
            if (onChange) {
                onChange(newContent);
            }
        },
        [onChange],
    );

    return (
        <div className={cn('space-y-1.5', containerClassName)}>
            {label && (
                <Label
                    htmlFor={id}
                    className={cn(
                        'text-sm font-medium',
                        disabled && 'opacity-60',
                    )}
                >
                    {label}
                    {required && <span className="ml-1 text-red-500">*</span>}
                </Label>
            )}

            <div
                className={cn(
                    'rounded-md overflow-hidden',
                    error && 'ring-2 ring-red-500',
                    disabled && 'opacity-60 pointer-events-none',
                )}
            >
                <JoditEditor
                    ref={editor}
                    value={value}
                    config={config}
                    tabIndex={1}
                    onBlur={handleBlur}
                    onChange={handleChange}
                />
            </div>

            {helperText && !error && (
                <p className="text-xs text-muted-foreground">{helperText}</p>
            )}

            {error && <p className="text-xs text-red-500">{error}</p>}

            {showPreview && value && (
                <div className="mt-4">
                    <Label className="text-sm font-medium">Preview:</Label>
                    <div
                        className="mt-2 rounded-md border bg-muted/50 p-4 prose prose-sm max-w-none"
                        dangerouslySetInnerHTML={{ __html: value }}
                    />
                </div>
            )}
        </div>
    );
};

export default TextEditor;
