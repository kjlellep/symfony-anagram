import React, { useState } from 'react';
import { importWordbase, findAnagrams } from './api';

function App() {
  const [importResult, setImportResult] = useState(null);
  const [word, setWord] = useState('');
  const [anagrams, setAnagrams] = useState([]);
  const [loading, setLoading] = useState(false);

  const handleImport = async () => {
    try {
      setLoading(true);
      const result = await importWordbase();
    
      if (result.status === 'already_imported') {
        setImportResult(`Wordbase already imported (${result.rows} rows).`);
      } else if (result.status === 'import_successful') {
        setImportResult(`Successfully imported ${result.rows} words.`);
      } else {
        alert("Unexpected import response.");
      }
    } catch (error) {
      console.error(error);
      const message = error.response?.data?.error || 'Import failed.';
      setImportResult(null);
      alert(message);
    } finally {
      setLoading(false);
    }
  };

  const handleAnagramSearch = async () => {
    if (!word.trim()) {
      setAnagrams([]);
      return;
    }

    try {
      const result = await findAnagrams(word);
      setAnagrams(result.anagrams);
    } catch (error) {
      console.error(error);
      setAnagrams([]);
      alert('Anagram search failed.');
    }
  };

  return (
    <div style={{
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'center',
      alignItems: 'center',
      minHeight: '100vh',
      fontFamily: 'sans-serif',
    }}>
      <div style={{
        width: '100%',
        maxWidth: '600px',
        padding: '20px',
        boxSizing: 'border-box',
        textAlign: 'center',
      }}>
        <h1>Anagram Finder</h1>

        <section style={{ marginBottom: '40px' }}>
          <h2>Import Wordbase</h2>
          <button onClick={handleImport} disabled={loading}>
            {loading ? 'Importing...' : 'Import'}
          </button>
          {importResult && (
            <pre>{JSON.stringify(importResult, null, 2)}</pre>
          )}
        </section>

        <section>
          <h2>Find Anagrams</h2>
          <input
            type="text"
            value={word}
            onChange={(e) => setWord(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === 'Enter') {
                handleAnagramSearch();
              }
            }}
          />
          <button onClick={handleAnagramSearch}>Search</button>
          {anagrams.length > 0 && (
            <ul style={{ listStyle: 'none', padding: 0 }}>
              {anagrams.map((anagram, index) => (
                <li key={index}>{anagram}</li>
              ))}
            </ul>
          )}
        </section>
      </div>
    </div>
  );
}

export default App;