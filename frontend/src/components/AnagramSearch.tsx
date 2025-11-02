import { useState, useCallback } from "react";
import { findAnagrams } from "../api";
import { errorMessage } from "../types";
import AnagramList from "./AnagramList";
import LoadingButton from "./LoadingButton";

export default function AnagramSearch() {
  const [word, setWord] = useState("");
  const [loading, setLoading] = useState(false);
  const [anagrams, setAnagrams] = useState<string[]>([]);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const doSearch = useCallback(async (): Promise<void> => {
    const trimmed = word.trim();

    if (!trimmed) {
      setAnagrams([]);
      setSubmitted(false);
      setError(null);
      return;
    }

    setLoading(true);
    setError(null);
    try {
      const res = await findAnagrams(trimmed);
      setAnagrams(res.anagrams ?? []);
      setSubmitted(true);
    } catch (e) {
      setError(errorMessage(e));
      setAnagrams([]);
      setSubmitted(true);
    } finally {
      setLoading(false);
    }
  }, [word]);

  const canSearch = word.trim().length > 0 && !loading;

  return (
    <section aria-busy={loading}>
      <h2>Find Anagrams</h2>

      <form
        onSubmit={(e) => {
          e.preventDefault();
          void doSearch();
        }}
      >
        <input
          id="word"
          type="text"
          value={word}
          onChange={(e) => setWord(e.target.value)}
          autoComplete="off"
          aria-label="Word"
        />
        <LoadingButton
          type="submit"
          onClick={doSearch}
          loading={loading}
          disabled={!canSearch}
          style={{ marginLeft: 8 }}
        >
          Search
        </LoadingButton>
      </form>

      {error && (
        <p role="alert" aria-live="polite" style={{ color: "red", marginTop: 8 }}>
          {error}
        </p>
      )}

      {submitted && anagrams.length === 0 && !loading && !error && (
        <p aria-live="polite" style={{ marginTop: 8 }}>
          No anagrams found.
        </p>
      )}

      <AnagramList anagrams={anagrams} />
    </section>
  );
}
