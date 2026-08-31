# TUFF BEATZ Production Portal V2

Production Portal V2 expands Start a Project from an Artist/Client form into an industry-wide production intake.

## Industry roles
Artist / Recording Artist, Group / Duo, Band, Singer / Vocalist, Songwriter, Producer, Composer, Musician, Record Label / A&R, Artist Manager / Representative, Publisher, Film / TV / Media Company, Church / Choir, Content Creator, and Other Industry Professional.

## Services
Full Song Production, Beat / Instrumental Production, Mixing, Mastering, Mixing + Mastering, Vocal Production, Songwriting, Arrangement, Musician / Instrument Recording, Band Production, Artist Development, Producer Collaboration, Film / TV / Media Music, Production Consultation, and Custom Projects.

## Intake workflow
1. Who are you?
2. What do you need?
3. Build the project — title, type, genre, BPM, key, stem count, budget, deadline, references, creative brief.
4. Mixing/mastering notes.
5. Multi-file upload or large-session transfer URL.
6. Rights/permission confirmation.
7. TUFF BEATZ review and production status workflow.

## File intake
The current WordPress implementation accepts up to 12 files per submission: WAV, AIFF/AIF, MP3, M4A, ZIP, MIDI, PDF, and TXT. A transfer-link field supports sessions larger than the WordPress/server upload limit.

Important: website uploads currently use WordPress attachment storage. This V2 release improves intake and workflow, but it is not yet the final private signed-download storage architecture. Private object storage / protected delivery should be a later security upgrade before using the portal as the sole repository for highly sensitive unreleased masters.

## Status workflow
New → Reviewing → Approved → In Production → Revision → Mastering → Final Delivery → Completed (or Declined).

## Visual protection
Production Portal V2 is isolated to the Start a Project template and `project-portal.css`. It must not modify the approved V3.4 public homepage baseline documented in `APPROVED-GOOD-VERSION.md`.
