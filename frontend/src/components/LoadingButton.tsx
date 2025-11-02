import { ButtonHTMLAttributes, FC } from "react";

interface LoadingButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  loading?: boolean;
}

const LoadingButton: FC<LoadingButtonProps> = ({ loading = false, children, disabled, ...rest }) => {
  return (
    <button
      {...rest}
      disabled={loading || disabled}
      aria-busy={loading}
      aria-disabled={loading || disabled}
    >
      {loading ? "Please wait…" : children}
    </button>
  );
};

export default LoadingButton;
