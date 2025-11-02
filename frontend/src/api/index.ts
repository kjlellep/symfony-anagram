import type { ImportResult, AnagramResponse, ApiErrorShape } from "../types";

async function getJson<T>(input: RequestInfo | URL, init?: RequestInit): Promise<T> {
  const res = await fetch(input, { cache: "no-store", ...init });

  const text = await res.text();
  const maybeJson = text ? (JSON.parse(text) as unknown) : null;

  if (!res.ok) {
    const apiErr = (maybeJson as Partial<ApiErrorShape> | null)?.error;
    throw new Error(apiErr ?? `Request failed with ${res.status}`);
  }

  return maybeJson as T;
}

export async function importWordbase(): Promise<ImportResult> {
  return getJson<ImportResult>("/api/import-wordbase", { method: "GET" });
}

export async function findAnagrams(word: string): Promise<AnagramResponse> {
  const url = new URL("/api/anagram", window.location.origin);
  url.searchParams.set("word", word);
  return getJson<AnagramResponse>(url, { method: "GET" });
}
