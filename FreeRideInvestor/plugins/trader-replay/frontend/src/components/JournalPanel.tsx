/**
 * Journal Panel Component
 * Timestamped notes with templates
 */

import React, { useState } from 'react';
import './JournalPanel.css';

interface JournalEntry {
  id?: number;
  timestamp: string;
  candle_index?: number;
  entry_type: string;
  content: string;
  emotion_tag?: string;
}

interface JournalPanelProps {
  entries: JournalEntry[];
  currentIndex: number;
  onAddEntry: (entry: Partial<JournalEntry>) => void;
}

export const JournalPanel: React.FC<JournalPanelProps> = ({ entries, currentIndex, onAddEntry }) => {
  const [showForm, setShowForm] = useState(false);
  const [entryType, setEntryType] = useState('note');
  const [content, setContent] = useState('');
  const [emotionTag, setEmotionTag] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!content.trim()) return;

    onAddEntry({
      entry_type: entryType,
      content: content.trim(),
      emotion_tag: emotionTag || undefined,
    });

    // Reset form
    setContent('');
    setEmotionTag('');
    setEntryType('note');
    setShowForm(false);
  };

  const filteredEntries = entries.filter((e) => e.candle_index === currentIndex || !e.candle_index);

  return (
    <div className="journal-panel">
      <div className="panel-header">
        <h3>📝 Journal</h3>
        <button className="add-btn" onClick={() => setShowForm(!showForm)}>
          {showForm ? '✕' : '+'}
        </button>
      </div>

      {showForm && (
        <form className="journal-form" onSubmit={handleSubmit}>
          <select
            value={entryType}
            onChange={(e) => setEntryType(e.target.value)}
            className="entry-type-select"
          >
            <option value="note">Note</option>
            <option value="setup">Setup</option>
            <option value="trigger">Trigger</option>
            <option value="risk">Risk</option>
            <option value="result">Result</option>
            <option value="lesson">Lesson</option>
          </select>

          <textarea
            value={content}
            onChange={(e) => setContent(e.target.value)}
            placeholder="Write your journal entry..."
            className="journal-textarea"
            rows={4}
          />

          <select
            value={emotionTag}
            onChange={(e) => setEmotionTag(e.target.value)}
            className="emotion-select"
          >
            <option value="">No emotion tag</option>
            <option value="confidence">Confidence</option>
            <option value="fear">Fear</option>
            <option value="greed">Greed</option>
            <option value="doubt">Doubt</option>
            <option value="calm">Calm</option>
            <option value="frustrated">Frustrated</option>
          </select>

          <button type="submit" className="submit-btn">
            Save Entry
          </button>
        </form>
      )}

      <div className="journal-entries">
        {filteredEntries.length === 0 ? (
          <p className="empty-state">No entries at this candle. Add one above!</p>
        ) : (
          filteredEntries.map((entry, idx) => (
            <div key={entry.id || idx} className="journal-entry">
              <div className="entry-header">
                <span className="entry-type">{entry.entry_type}</span>
                {entry.emotion_tag && (
                  <span className="emotion-tag">{entry.emotion_tag}</span>
                )}
              </div>
              <p className="entry-content">{entry.content}</p>
              <div className="entry-footer">
                {entry.timestamp && new Date(entry.timestamp).toLocaleTimeString()}
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};



