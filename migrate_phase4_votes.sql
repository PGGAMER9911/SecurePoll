-- Migration Phase 4: Allow multiple choice voting array storage
-- Date: 2026-09-18
-- Description: Drops the poll_id unique index and replaces it with poll_id_voter_opt.

ALTER TABLE votes ADD UNIQUE INDEX poll_id_voter_opt (poll_id, voter_hash, option_id);
ALTER TABLE votes DROP INDEX poll_id;
