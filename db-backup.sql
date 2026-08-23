-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.users (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  username character varying NOT NULL,
  password character varying,
  role character varying,
  is_active boolean DEFAULT true,
  first_name text,
  last_name text,
  CONSTRAINT users_pkey PRIMARY KEY (id)
);
CREATE TABLE public.semesters (
  sem_id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  semester_name text NOT NULL,
  semester_start date,
  semester_end date,
  semester_status text,
  is_archived boolean,
  CONSTRAINT semesters_pkey PRIMARY KEY (sem_id)
);
CREATE TABLE public.scholars (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  user_id bigint,
  username text,
  barangay text,
  phonenumber bigint,
  email text,
  verification_token text,
  qrtoken text,
  verification_token_generated_at timestamp without time zone,
  CONSTRAINT scholars_pkey PRIMARY KEY (id),
  CONSTRAINT scholars_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);
CREATE TABLE public.activities (
  activity_id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  activityname text NOT NULL,
  activitydate date,
  timein_start time without time zone,
  timein_end time without time zone,
  venue text,
  classification text,
  barangay text,
  info text,
  semester bigint,
  timeout_start time without time zone,
  timeout_end time without time zone,
  CONSTRAINT activities_pkey PRIMARY KEY (activity_id)
);
CREATE TABLE public.attendance_record (
  ar_id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  activityid bigint NOT NULL,
  date date,
  timein time without time zone,
  timeout time without time zone,
  attendance_status text DEFAULT 'absent'::text,
  scanned_by bigint,
  sync_status text DEFAULT 'pending'::text,
  user_id bigint,
  CONSTRAINT attendance_record_pkey PRIMARY KEY (ar_id),
  CONSTRAINT fk_attendance_user FOREIGN KEY (user_id) REFERENCES public.users(id),
  CONSTRAINT fk_scanned_by_user FOREIGN KEY (scanned_by) REFERENCES public.users(id),
  CONSTRAINT attendance_record_activity_id_fkey FOREIGN KEY (activityid) REFERENCES public.activities(activity_id)
);
CREATE TABLE public.officers (
  officer_id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  user_id bigint NOT NULL,
  officerno text,
  barangay text,
  email text,
  phonenumber text,
  CONSTRAINT officers_pkey PRIMARY KEY (officer_id),
  CONSTRAINT officers_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);