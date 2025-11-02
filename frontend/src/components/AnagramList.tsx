export default function AnagramList({ anagrams }: { anagrams: string[] }) {
  if (anagrams.length === 0) return null;
  return (
    <ul style={{ listStyle: "none", padding: 0 }}>
      {anagrams.map((a, i) => (
        <li key={`${a}-${i}`}>{a}</li>
      ))}
    </ul>
  );
}
