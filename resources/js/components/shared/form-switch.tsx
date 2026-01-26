import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';

interface FormSwitchProps {
    label?: string;
    description?: string;
    checked: boolean;
    error?: string;
    disabled?: boolean;
    onChange: (checked: boolean) => void;
    containerClassName?: string;
}

export default function FormSwitch({
    label,
    description,
    checked,
    error,
    disabled,
    onChange,
    containerClassName,
}: FormSwitchProps) {
    return (
        <div className={cn('space-y-1.5', containerClassName)}>
            <div className="flex items-center justify-between">
                <div className="space-y-0.5">
                    {label && (
                        <Label className={cn(disabled && 'opacity-60')}>
                            {label}
                        </Label>
                    )}

                    {description && (
                        <p className="text-xs text-muted-foreground">
                            {description}
                        </p>
                    )}
                </div>

                <Switch
                    checked={checked}
                    disabled={disabled}
                    onCheckedChange={onChange}
                />
            </div>

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}
