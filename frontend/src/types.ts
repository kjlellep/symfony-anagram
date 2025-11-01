export type ImportStatus = "already_imported" | "import_successful";

export interface ImportResult {
  status: ImportStatus;
  rows: number;
}

export interface AnagramResponse {
  input: string;
  anagrams: string[];
}

export interface ApiErrorShape {
  error: string;
}

export function errorMessage(err: unknown): string {
  if (err instanceof Error) return err.message;
  if (typeof err === "string") return err;
  try {
    const anyErr = err as { response?: { data?: Partial<ApiErrorShape> } };
    return anyErr?.response?.data?.error ?? "Unexpected error";
  } catch {
    return "Unexpected error";
  }
}
