import { Suspense, lazy } from "react";
import "./App.css";

const ImportSection = lazy(() => import("./components/ImportSection"));
const AnagramSearch = lazy(() => import("./components/AnagramSearch"));

export default function App() {
  return (
    <main className="app" role="main" aria-label="Anagram Finder App">
      <div className="app__inner">
        <h1 className="app__title">Anagram Finder</h1>

        <Suspense fallback={<p className="app__loading">Loading…</p>}>
          <ImportSection />
          <AnagramSearch />
        </Suspense>
      </div>
    </main>
  );
}
