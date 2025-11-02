import { useCallback, useState } from "react";
import { importWordbase } from "../api";
import { errorMessage, type ImportResult } from "../types";
import LoadingButton from "./LoadingButton";

function messageFromResult(res: ImportResult): string {
  if (res.status === "already_imported") {
    return `Wordbase already imported (${res.rows} rows).`;
  }
  if (res.status === "import_successful") {
    return `Successfully imported ${res.rows} words.`;
  }
  return "Import completed.";
}

export default function ImportSection() {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<ImportResult | null>(null);
  const [err, setErr] = useState<string | null>(null);

  const handleImport = useCallback(async (): Promise<void> => {
    if (loading) return;
    setLoading(true);
    setErr(null);
    try {
      const res = await importWordbase();
      setResult(res);
    } catch (e) {
      setResult(null);
      setErr(errorMessage(e));
    } finally {
      setLoading(false);
    }
  }, [loading]);

  return (
    <section style={{ marginBottom: 40 }} aria-busy={loading}>
      <h2>Import Wordbase</h2>
      <LoadingButton
        type="button"
        onClick={handleImport}
        loading={loading}
        disabled={loading}
      >
        Import
      </LoadingButton>

      {err && (
        <p role="alert" aria-live="polite" style={{ color: "red", marginTop: 12 }}>
          {err}
        </p>
      )}

      {result && !err && (
        <p aria-live="polite" style={{ marginTop: 12 }}>
          {messageFromResult(result)}
        </p>
      )}
    </section>
  );
}
