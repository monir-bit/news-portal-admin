import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import * as React from 'react';

interface FormInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    helperText?: string;
    required?: boolean;
    leftIcon?: React.ReactNode;
    rightIcon?: React.ReactNode;
    containerClassName?: string;
}

const FormInput = React.forwardRef<HTMLInputElement, FormInputProps>(
    (
        {
            label,
            error,
            helperText,
            required,
            leftIcon,
            rightIcon,
            disabled,
            id,
            className,
            containerClassName,
            ...props
        },
        ref,
    ) => {
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
                        {required && (
                            <span className="ml-1 text-red-500">*</span>
                        )}
                    </Label>
                )}

                <div className="relative">
                    {leftIcon && (
                        <div className="absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground">
                            {leftIcon}
                        </div>
                    )}

                    <Input
                        ref={ref}
                        id={id}
                        disabled={disabled}
                        className={cn(
                            leftIcon && 'pl-10',
                            rightIcon && 'pr-10',
                            error &&
                                'border-red-500 focus-visible:ring-red-500',
                            className,
                        )}
                        {...props}
                    />

                    {rightIcon && (
                        <div className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground">
                            {rightIcon}
                        </div>
                    )}
                </div>

                {helperText && !error && (
                    <p className="text-xs text-muted-foreground">
                        {helperText}
                    </p>
                )}

                {error && <p className="text-xs text-red-500">{error}</p>}
            </div>
        );
    },
);

FormInput.displayName = 'FormInput';

export default FormInput;
