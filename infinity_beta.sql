-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 29, 2024 at 01:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infinity_beta`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendars`
--

CREATE TABLE `academic_calendars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `title` varchar(512) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `table_type` varchar(191) DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `instructions` varchar(1024) DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `points` int(11) DEFAULT NULL,
  `resubmission` tinyint(1) NOT NULL DEFAULT 0,
  `extra_days_for_resubmission` int(11) DEFAULT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `feedback` text DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=Absent, 1=Present',
  `date` date NOT NULL,
  `remark` varchar(512) NOT NULL,
  `status` bigint(20) DEFAULT 0 COMMENT '0 - not send 1 - send',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SC', 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 'ST', 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, 'OBC', 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(4, 'General', 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chat_files`
--

CREATE TABLE `chat_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_members`
--

CREATE TABLE `chat_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `modal_type` varchar(191) NOT NULL,
  `modal_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text DEFAULT NULL,
  `date` datetime DEFAULT '2024-07-29 06:21:57',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `medium_id` int(11) NOT NULL,
  `stream_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `medium_id`, `stream_id`, `shift_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'إولى ثانوي', 2, NULL, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 'ثانيه ثانوي', 2, NULL, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, 'G10', 3, NULL, NULL, '2024-07-29 07:29:43', '2024-07-29 07:29:43', NULL),
(4, 'G11', 3, NULL, NULL, '2024-07-29 07:30:03', '2024-07-29 07:30:03', NULL),
(5, 'G12', 3, NULL, NULL, '2024-07-29 07:30:16', '2024-07-29 07:30:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_sections`
--

CREATE TABLE `class_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_sections`
--

INSERT INTO `class_sections` (`id`, `class_id`, `section_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 1, 2, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, 2, 1, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(4, 2, 2, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(5, 2, 3, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(6, 3, 1, NULL, NULL, NULL),
(7, 4, 1, NULL, NULL, NULL),
(8, 5, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL COMMENT 'Compulsory / Elective',
  `subject_id` int(11) NOT NULL,
  `elective_subject_group_id` int(11) DEFAULT NULL COMMENT 'if type=Elective',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_teachers`
--

CREATE TABLE `class_teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_teachers`
--

INSERT INTO `class_teachers` (`id`, `class_section_id`, `class_teacher_id`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2024-07-29 04:22:02', '2024-07-29 04:22:02'),
(2, 6, 2, '2024-07-29 07:42:34', '2024-07-29 07:42:34'),
(3, 7, 2, '2024-07-29 07:42:46', '2024-07-29 07:42:46'),
(4, 8, 2, '2024-07-29 07:42:55', '2024-07-29 07:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `date` date DEFAULT NULL,
  `message` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `expiry_date` timestamp NULL DEFAULT NULL,
  `maximum_discount` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `maximum_usage` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `type` enum('charge_amount','purchase','discount') NOT NULL DEFAULT 'purchase',
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `only_applied_to_type` varchar(191) DEFAULT NULL,
  `only_applied_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usages`
--

CREATE TABLE `coupon_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED NOT NULL,
  `used_by_user_type` varchar(191) NOT NULL,
  `used_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `applied_to_type` varchar(191) NOT NULL,
  `applied_to_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,3) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `educational_programs`
--

CREATE TABLE `educational_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `elective_subject_groups`
--

CREATE TABLE `elective_subject_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total_subjects` int(11) NOT NULL,
  `total_selectable_subjects` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `lesson_id`, `user_id`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 2, 4, NULL, '2024-07-29 04:37:21', '2024-07-29 04:37:21'),
(2, 4, 4, NULL, '2024-07-29 08:13:15', '2024-07-29 08:13:15');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'single',
  `end_time` time DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `session_year_id` int(11) NOT NULL,
  `publish` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_classes`
--

CREATE TABLE `exam_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_marks`
--

CREATE TABLE `exam_marks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_timetable_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `obtained_marks` int(11) NOT NULL,
  `teacher_review` varchar(1024) DEFAULT NULL,
  `passing_status` tinyint(1) NOT NULL COMMENT '1=Pass, 0=Fail',
  `session_year_id` int(11) NOT NULL,
  `grade` tinytext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `obtained_marks` int(11) NOT NULL,
  `percentage` double(8,2) NOT NULL,
  `grade` tinytext NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_timetables`
--

CREATE TABLE `exam_timetables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` varchar(1024) NOT NULL,
  `answer` varchar(1024) NOT NULL,
  `status` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_choiceables`
--

CREATE TABLE `fees_choiceables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `fees_type_id` int(11) DEFAULT NULL,
  `is_due_charges` tinyint(4) NOT NULL COMMENT '0 - no 1 - yes',
  `total_amount` double NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `payment_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` bigint(20) DEFAULT 0 COMMENT '0 - not paid 1 - paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_classes`
--

CREATE TABLE `fees_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` int(11) NOT NULL,
  `fees_type_id` int(11) NOT NULL,
  `choiceable` tinyint(4) NOT NULL COMMENT '0 - no 1 - yes',
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_paids`
--

CREATE TABLE `fees_paids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `mode` smallint(6) DEFAULT NULL COMMENT '0 - cash 1 - cheque 2 - online',
  `payment_transaction_id` varchar(191) DEFAULT NULL,
  `cheque_no` varchar(191) DEFAULT NULL,
  `total_amount` double NOT NULL,
  `is_fully_paid` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 - no 1 - yes',
  `date` date NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_types`
--

CREATE TABLE `fees_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `modal_type` varchar(191) NOT NULL,
  `modal_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(1024) DEFAULT NULL,
  `file_thumbnail` varchar(1024) DEFAULT NULL,
  `type` tinytext NOT NULL COMMENT '1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link',
  `file_url` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `modal_type`, `modal_id`, `file_name`, `file_thumbnail`, `type`, `file_url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'App\\Models\\Lesson', 2, 'name 45', 'lessons/1722227324-pngegg (4).png', '5', 'https://video-corner.com/iframe/29/e0020fb0-162b-436f-a56a-a5d741746df0', '2024-07-29 04:28:45', '2024-07-29 04:28:45', NULL),
(2, 'App\\Models\\LessonTopic', 1, 'name topic 45', 'lessons/1722227389-pngegg (19).png', '5', 'https://video-corner.com/iframe/29/e0020fb0-162b-436f-a56a-a5d741746df0', '2024-07-29 04:29:49', '2024-07-29 04:29:49', NULL),
(4, 'App\\Models\\LessonTopic', 3, 'name 88', 'lessons/1722240387-pngegg (4).png', '5', 'https://video-corner.com/iframe/29/e0020fb0-162b-436f-a56a-a5d741746df0', '2024-07-29 08:06:27', '2024-07-29 08:06:27', NULL),
(5, 'App\\Models\\LessonTopic', 4, 'Session1', 'lessons/1722240706-WhatsApp Image 2024-07-29 at 10.39.53.jpeg', '5', 'https://video-corner.com/iframe/29/e0020fb0-162b-436f-a56a-a5d741746df0', '2024-07-29 08:11:46', '2024-07-29 08:11:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL COMMENT 'text,number,textarea,dropdown,checkbox,radio,fileupload',
  `for` varchar(191) DEFAULT NULL COMMENT '1- student, 2-parent ,3-teacher',
  `is_required` tinyint(4) NOT NULL DEFAULT 0,
  `default_values` text DEFAULT NULL COMMENT 'values of radio,checkbox,dropdown,etc',
  `other` text DEFAULT NULL COMMENT 'extra HTML attributes',
  `rank` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `starting_range` int(11) NOT NULL,
  `ending_range` int(11) NOT NULL,
  `grade` tinytext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `installment_fees`
--

CREATE TABLE `installment_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `due_date` date NOT NULL,
  `due_charges` int(11) NOT NULL COMMENT 'in percentage (%)',
  `session_year_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `code` varchar(64) NOT NULL,
  `file` varchar(512) NOT NULL,
  `is_rtl` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=>active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `leave_master_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(191) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `status` varchar(191) NOT NULL COMMENT '0- pending, 1- approved, 3- rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_details`
--

CREATE TABLE `leave_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_masters`
--

CREATE TABLE `leave_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total_leave` varchar(191) NOT NULL COMMENT 'Leaves per month',
  `holiday_days` varchar(191) NOT NULL,
  `session_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `class_section_id` int(11) NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('published','archived','draft') NOT NULL DEFAULT 'draft',
  `subject_id` int(11) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = free , 1 = paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `name`, `description`, `class_section_id`, `teacher_id`, `status`, `subject_id`, `is_paid`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'name 45', 'description 25', 3, 1, 'published', 4, 1, '2024-07-29 04:28:44', '2024-07-29 04:28:44', NULL),
(3, 'August', 'Math Lo1 and Mechanics Lo1', 6, 2, 'published', 9, 1, '2024-07-29 07:53:57', '2024-07-29 07:53:57', NULL),
(4, 'name 88', 'description 88', 3, 1, 'published', 4, 1, '2024-07-29 08:05:10', '2024-07-29 08:05:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_topics`
--

CREATE TABLE `lesson_topics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medias`
--

CREATE TABLE `medias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `type` int(11) NOT NULL COMMENT '1-image 2-video',
  `thumbnail` varchar(191) DEFAULT NULL,
  `youtube_url` varchar(191) DEFAULT NULL,
  `session_year_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE `media_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_url` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mediums`
--

CREATE TABLE `mediums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mediums`
--

INSERT INTO `mediums` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'British School', NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(2, 'American School', NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(3, 'STEM School', NULL, '2024-07-29 04:22:01', '2024-07-29 07:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_04_01_044234_create_settings_table', 1),
(6, '2022_04_01_091033_create_permission_tables', 1),
(7, '2022_04_01_105826_all_tables', 1),
(8, '2022_04_27_072441_parent_changes', 1),
(9, '2022_04_28_105419_add_day_name_to_timetables_table', 1),
(10, '2022_04_29_164836_add_class_section_id_to_timetables', 1),
(11, '2022_05_03_053843_add_lesson_files', 1),
(12, '2022_05_06_071034_create_holidays_table', 1),
(13, '2022_05_11_063841_add_sliders', 1),
(14, '2022_05_13_041458_add_date_to_session_years_table', 1),
(15, '2022_05_16_045021_add_class_secion_id_to_attendances', 1),
(16, '2022_05_19_053446_add_fcm_id_to_users', 1),
(17, '2022_05_31_133456_add_reset_request_to_users', 1),
(18, '2022_06_03_060653_create_student_sessions_table', 1),
(19, '2022_06_07_065946_create_languages_table', 1),
(20, '2022_07_18_044243_is_rtl_in_language', 1),
(21, '2022_07_25_103347_create_exams_table', 1),
(22, '2022_11_11_065720_fees_module', 1),
(23, '2022_12_08_044452_generate_roll_number', 1),
(24, '2022_12_12_033204_online_exam_module', 1),
(25, '2023_02_14_164618_update_online_exam_to_class_section', 1),
(26, '2023_06_02_100137_change_fee_choiceable_to_class', 1),
(27, '2023_06_02_100328_create_installment_fees_table', 1),
(28, '2023_06_05_104000_create_paid_installment_fees_table', 1),
(29, '2023_07_04_095806_create_streams_table', 1),
(30, '2023_07_06_101005_add_column_stream_id_in_classes_table', 1),
(31, '2023_07_11_095636_create_class_teachers_table', 1),
(32, '2023_07_11_101343_drop_column_class_teacher_id_from_class_sections', 1),
(33, '2023_07_14_092845_create_shifts_table', 1),
(34, '2023_07_18_101604_add_column_shift_id_in_classes', 1),
(35, '2023_07_25_123007_drop_column_deleted_at_from_class_teachers', 1),
(36, '2023_09_23_131406_add_column_status_in_fees_choiceables', 1),
(37, '2023_09_23_131627_add_column_status', 1),
(38, '2023_09_26_123001_add_column_status_in_assignment_submissions', 1),
(39, '2023_10_11_165326_create_dynamic_form_fields', 1),
(40, '2023_10_18_115940_create_notifications', 1),
(41, '2023_10_19_161317_add_column_device_type_in_users_table', 1),
(42, '2023_10_20_105920_drop_column_in_notifications', 1),
(43, '2023_10_23_110510_create_user_notifications', 1),
(44, '2023_10_27_153258_add_column_is_custom_in_notifications', 1),
(45, '2023_12_08_122524_all_chat_table', 1),
(46, '2024_01_30_165655_add_type_to_sliders_table', 1),
(47, '2024_02_02_111843_create_all_tables', 1),
(48, '2024_03_12_135926_add_column_type_in_events_table', 1),
(49, '2024_03_15_173052_create_staffs_table', 1),
(50, '2024_03_19_153704_create_multiple_events_table', 1),
(51, '2024_04_16_154756_add_column_for_in_form_fields_table', 1),
(52, '2024_04_25_110112_remove_editing_column_from_assignment_submissions_table', 1),
(53, '2024_05_27_125009_make_dynamic_fields_column_nullable_in_tables', 1),
(54, '2024_05_28_153727_add_column_in_timetables_table', 1),
(55, '2024_05_29_172900_create_all_leave_manage_table', 1),
(56, '2024_06_25_094803_add_column_free_app_use_date_in_session_years_table', 1),
(57, '2024_07_13_122244_create_coupons_table', 1),
(58, '2024_07_13_164041_create_coupon_usages_table', 1),
(59, '2024_07_20_131631_create_enrollments_table', 1),
(60, '2024_07_27_163226_add_fields_to_coupons_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(109, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `multiple_events`
--

CREATE TABLE `multiple_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `message` varchar(128) NOT NULL,
  `send_to` int(11) NOT NULL,
  `image` varchar(512) DEFAULT NULL,
  `date` datetime NOT NULL,
  `is_custom` bigint(20) DEFAULT NULL COMMENT '1-custom, 0-autogenerated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exams`
--

CREATE TABLE `online_exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL,
  `exam_key` bigint(20) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'in minutes',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `session_year_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_questions`
--

CREATE TABLE `online_exam_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_subject_id` bigint(20) UNSIGNED NOT NULL,
  `question_type` tinyint(4) NOT NULL COMMENT '0 - simple 1 - equation based',
  `question` varchar(1024) NOT NULL,
  `image_url` varchar(1024) DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_question_answers`
--

CREATE TABLE `online_exam_question_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` bigint(20) UNSIGNED NOT NULL COMMENT 'option id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_question_choices`
--

CREATE TABLE `online_exam_question_choices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `online_exam_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `marks` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_question_options`
--

CREATE TABLE `online_exam_question_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option` varchar(1024) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_student_answers`
--

CREATE TABLE `online_exam_student_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `online_exam_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL COMMENT 'online exam question choice id',
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `submitted_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paid_installment_fees`
--

CREATE TABLE `paid_installment_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `installment_fee_id` bigint(20) UNSIGNED NOT NULL,
  `session_year_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double(8,2) NOT NULL,
  `due_charges` double(8,2) DEFAULT NULL,
  `date` date NOT NULL,
  `payment_transaction_id` bigint(20) UNSIGNED NOT NULL,
  `status` bigint(20) DEFAULT 0 COMMENT '0 - not paid 1 - paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `gender` varchar(16) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `mobile` varchar(16) NOT NULL,
  `occupation` varchar(128) NOT NULL,
  `dynamic_fields` text DEFAULT NULL,
  `image` varchar(1024) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `user_id`, `first_name`, `last_name`, `gender`, `email`, `mobile`, `occupation`, `dynamic_fields`, `image`, `dob`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Sachin', 'Tendulkar', 'Male', 'father@gmail.com', '1234567890', 'Cricketer', NULL, 'parents/user.png', NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 3, 'Ajit', 'Tendulkar', 'Male', 'guardian@gmail.com', '1234567890', 'Job', NULL, 'parents/user.png', NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `mode` smallint(6) NOT NULL DEFAULT 2 COMMENT '0 - cash 1 - cheque 2 - online',
  `cheque_no` varchar(191) DEFAULT NULL,
  `type_of_fee` smallint(6) NOT NULL DEFAULT 0 COMMENT '0 - compulosry_full , 1 - installments , 2 -optional',
  `payment_gateway` smallint(6) DEFAULT NULL COMMENT '1 - razorpay 2 - stripe',
  `order_id` varchar(191) DEFAULT NULL COMMENT 'order_id / payment_intent_id',
  `payment_id` varchar(191) DEFAULT NULL,
  `payment_signature` varchar(191) DEFAULT NULL,
  `payment_status` tinyint(4) NOT NULL COMMENT '0 - failed 1 - succeed 2 - pending',
  `total_amount` double NOT NULL,
  `date` datetime DEFAULT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'role-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(2, 'role-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(3, 'role-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(4, 'role-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(5, 'medium-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(6, 'medium-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(7, 'medium-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(8, 'medium-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(9, 'section-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(10, 'section-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(11, 'section-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(12, 'section-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(13, 'class-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(14, 'class-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(15, 'class-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(16, 'class-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(17, 'subject-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(18, 'subject-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(19, 'subject-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(20, 'subject-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(21, 'teacher-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(22, 'teacher-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(23, 'teacher-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(24, 'teacher-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(25, 'class-teacher-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(26, 'class-teacher-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(27, 'class-teacher-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(28, 'class-teacher-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(29, 'parents-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(30, 'parents-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(31, 'parents-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(32, 'parents-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(33, 'session-year-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(34, 'session-year-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(35, 'session-year-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(36, 'session-year-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(37, 'student-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(38, 'student-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(39, 'student-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(40, 'student-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(41, 'category-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(42, 'category-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(43, 'category-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(44, 'category-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(45, 'subject-teacher-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(46, 'subject-teacher-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(47, 'subject-teacher-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(48, 'subject-teacher-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(49, 'timetable-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(50, 'timetable-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(51, 'timetable-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(52, 'timetable-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(53, 'attendance-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(54, 'attendance-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(55, 'attendance-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(56, 'attendance-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(57, 'holiday-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(58, 'holiday-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(59, 'holiday-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(60, 'holiday-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(61, 'announcement-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(62, 'announcement-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(63, 'announcement-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(64, 'announcement-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(65, 'slider-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(66, 'slider-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(67, 'slider-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(68, 'slider-delete', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(69, 'class-timetable', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(70, 'teacher-timetable', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(71, 'student-assignment', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(72, 'subject-lesson', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(73, 'class-attendance', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(74, 'exam-create', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(75, 'exam-list', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(76, 'exam-edit', 'web', '2024-07-29 04:21:58', '2024-07-29 04:21:58'),
(77, 'exam-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(78, 'exam-upload-marks', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(79, 'setting-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(80, 'fcm-setting-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(81, 'assignment-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(82, 'assignment-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(83, 'assignment-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(84, 'assignment-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(85, 'assignment-submission', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(86, 'email-setting-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(87, 'privacy-policy', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(88, 'contact-us', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(89, 'about-us', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(90, 'student-reset-password', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(91, 'reset-password-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(92, 'student-change-password', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(93, 'promote-student-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(94, 'promote-student-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(95, 'promote-student-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(96, 'promote-student-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(97, 'language-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(98, 'language-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(99, 'language-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(100, 'language-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(101, 'lesson-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(102, 'lesson-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(103, 'lesson-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(104, 'lesson-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(105, 'topic-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(106, 'topic-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(107, 'topic-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(108, 'topic-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(109, 'class-teacher', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(110, 'terms-condition', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(111, 'assign-class-to-new-student', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(112, 'exam-timetable-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(113, 'grade-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(114, 'update-admin-profile', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(115, 'exam-result', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(116, 'fees-type', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(117, 'fees-classes', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(118, 'fees-paid', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(119, 'fees-config', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(120, 'manage-online-exam', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(121, 'stream-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(122, 'stream-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(123, 'stream-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(124, 'stream-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(125, 'shift-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(126, 'shift-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(127, 'shift-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(128, 'shift-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(129, 'form-field-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(130, 'form-field-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(131, 'form-field-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(132, 'form-field-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(133, 'notification-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(134, 'notification-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(135, 'notification-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(136, 'notification-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(137, 'event-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(138, 'event-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(140, 'event-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(141, 'event-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(142, 'program-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(143, 'program-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(144, 'program-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(145, 'program-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(146, 'media-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(147, 'media-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(148, 'media-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(149, 'media-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(150, 'faq-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(151, 'faq-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(152, 'faq-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(153, 'faq-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(154, 'content-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(155, 'content-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(156, 'content-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(157, 'staff-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(158, 'staff-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(159, 'staff-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(160, 'staff-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(161, 'generate-id-card', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(162, 'generate-document', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(163, 'generate-result', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(164, 'leave-setting-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(165, 'leave-create', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(166, 'leave-delete', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(167, 'leave-edit', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(168, 'leave-list', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(169, 'leave-approve', 'web', '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(170, 'coupons-list', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(171, 'coupons-create', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(172, 'coupons-edit', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(173, 'coupons-delete', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(174, 'enrollments-list', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(175, 'enrollments-create', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(176, 'enrollments-delete', 'web', '2024-07-29 04:22:01', '2024-07-29 04:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `read_messages`
--

CREATE TABLE `read_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `modal_type` varchar(191) NOT NULL,
  `modal_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_read_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `custom_role` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `custom_role`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', 0, '2024-07-29 04:21:59', '2024-07-29 04:21:59'),
(2, 'Teacher', 'web', 0, '2024-07-29 04:22:00', '2024-07-29 04:22:00'),
(3, 'Parent', 'web', 0, '2024-07-29 04:22:01', '2024-07-29 04:22:01'),
(4, 'Student', 'web', 0, '2024-07-29 04:22:01', '2024-07-29 04:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(37, 2),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(45, 2),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(49, 2),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(69, 2),
(70, 1),
(70, 2),
(71, 1),
(71, 2),
(72, 1),
(72, 2),
(73, 1),
(73, 2),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 2),
(79, 1),
(80, 1),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 2),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 2),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(148, 1),
(149, 1),
(150, 1),
(151, 1),
(152, 1),
(153, 1),
(154, 1),
(155, 1),
(156, 1),
(157, 1),
(158, 1),
(159, 1),
(160, 1),
(161, 1),
(161, 2),
(162, 1),
(163, 1),
(163, 2),
(164, 1),
(165, 2),
(166, 1),
(166, 2),
(167, 2),
(168, 1),
(168, 2),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'A', '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 'B', '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, 'C', '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `session_years`
--

CREATE TABLE `session_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `free_app_use_date` date DEFAULT NULL,
  `default` tinyint(4) NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `include_fee_installments` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - no 1 - yes',
  `fee_due_date` date NOT NULL DEFAULT '2024-07-29',
  `fee_due_charges` int(11) NOT NULL DEFAULT 0 COMMENT 'in percentage (%)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `session_years`
--

INSERT INTO `session_years` (`id`, `name`, `free_app_use_date`, `default`, `start_date`, `end_date`, `include_fee_installments`, `fee_due_date`, `fee_due_charges`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2022-23', NULL, 1, '2022-06-01', '2023-04-30', 0, '2024-07-29', 0, '2024-07-29 04:22:01', '2024-07-29 08:26:22', NULL),
(2, '2023', NULL, 0, '2023-06-01', '2024-04-30', 0, '2024-07-29', 0, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, '2024', NULL, 0, '2024-06-01', '2025-04-30', 0, '2024-07-29', 0, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(4, '2025', NULL, 0, '2025-06-01', '2026-04-30', 0, '2024-07-29', 0, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` text NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `type`, `message`) VALUES
(1, 'school_name', 'Infinity School'),
(2, 'school_email', 'info@infinityschool.net'),
(3, 'school_phone', '+201500906883'),
(4, 'school_address', 'Egypt-Ismailia'),
(5, 'time_zone', 'Asia/Kolkata'),
(6, 'date_formate', 'd-m-Y'),
(7, 'time_formate', 'h:i A'),
(8, 'theme_color', '#4C5EA6'),
(9, 'session_year', '1'),
(10, 'secondary_color', '#38A3A5'),
(11, 'system_version', '3.2.0'),
(12, 'mail_host', 'smtp.gmail.com'),
(13, 'mail_mailer', 'smtp'),
(14, 'mail_port', '587'),
(15, 'mail_username', 'engelshafiy6@gmail.com'),
(16, 'mail_password', 'andwvfrorxacchcv'),
(17, 'mail_encryption', 'tls'),
(18, 'mail_send_from', 'engelshafiy6@gmail.com'),
(19, 'email_configration_verification', '1'),
(20, 'show_teachers', 'allow'),
(21, 'session_year', '1'),
(22, 'school_tagline', 'Infinity'),
(23, 'online_payment', '1'),
(24, 'facebook', 'cvzxcv'),
(25, 'instagram', 'vxcv'),
(26, 'linkedin', 'vxcv'),
(27, 'maplink', 'vxcv'),
(28, 'logo1', 'logo/vEDeZNtVEl36OWwHCegzf3KDg4UP54Dbowo8UnSJ.svg'),
(29, 'logo2', 'logo/aUzWy1hLHTfBKP9JChfzFB8czwNYIoVIdwOGCdd0.svg'),
(30, 'favicon', 'logo/67RABSEXoebQcgxH3kXjivuJ1t34PxijzwOT8ye2.svg');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(1024) NOT NULL,
  `type` varchar(191) DEFAULT NULL COMMENT '1- app, 2-web ,3-both',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `streams`
--

CREATE TABLE `streams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `admission_no` varchar(512) NOT NULL,
  `roll_number` int(11) DEFAULT NULL,
  `caste` varchar(128) DEFAULT NULL,
  `religion` varchar(128) DEFAULT NULL,
  `admission_date` date NOT NULL,
  `blood_group` varchar(32) DEFAULT NULL,
  `height` varchar(32) DEFAULT NULL,
  `weight` varchar(64) DEFAULT NULL,
  `is_new_admission` tinyint(4) NOT NULL DEFAULT 1,
  `father_id` int(11) DEFAULT NULL,
  `mother_id` int(11) DEFAULT NULL,
  `guardian_id` int(11) DEFAULT NULL,
  `dynamic_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_section_id`, `category_id`, `admission_no`, `roll_number`, `caste`, `religion`, `admission_date`, `blood_group`, `height`, `weight`, `is_new_admission`, `father_id`, `mother_id`, `guardian_id`, `dynamic_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 3, 1, '12345667', 1, 'Hindu', 'Hindu', '2024-04-01', 'B+', '5.5', '59', 1, 1, 2, 3, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_online_exam_statuses`
--

CREATE TABLE `student_online_exam_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `online_exam_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1 - in progress 2 - completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_sessions`
--

CREATE TABLE `student_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `result` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=>Pass,0=>fail',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=>continue,0=>leave',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `session_year_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(512) NOT NULL,
  `code` varchar(64) DEFAULT NULL,
  `bg_color` varchar(32) NOT NULL,
  `image` varchar(512) NOT NULL,
  `medium_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL COMMENT 'Theory / Practical',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_teachers`
--

CREATE TABLE `subject_teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_teachers`
--

INSERT INTO `subject_teachers` (`id`, `class_section_id`, `subject_id`, `teacher_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 6, 1, '2024-07-29 04:22:02', '2024-07-29 04:26:52', '2024-07-29 04:26:52'),
(2, 3, 4, 1, '2024-07-29 04:27:17', '2024-07-29 04:27:17', NULL),
(3, 6, 9, 2, '2024-07-29 07:43:14', '2024-07-29 07:43:14', NULL),
(4, 7, 9, 2, '2024-07-29 07:43:25', '2024-07-29 07:43:25', NULL),
(5, 8, 9, 2, '2024-07-29 07:43:35', '2024-07-29 07:43:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `qualification` varchar(512) NOT NULL,
  `dynamic_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `qualification`, `dynamic_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, '', NULL, '2024-07-29 04:22:02', '2024-07-29 04:22:02', NULL),
(2, 6, 'Math Teacher', '[]', '2024-07-29 07:41:24', '2024-07-29 07:41:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_teacher_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `link_name` varchar(191) DEFAULT NULL,
  `live_class_url` varchar(191) DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL,
  `day` int(11) NOT NULL COMMENT '1=monday,2=tuesday,3=wednesday,4=thursday,5=friday,6=saturday,7=sunday',
  `day_name` varchar(512) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `gender` varchar(16) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `fcm_id` varchar(1024) DEFAULT NULL,
  `device_type` varchar(128) DEFAULT NULL COMMENT 'android, ios',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `image` varchar(512) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `current_address` varchar(191) DEFAULT NULL,
  `permanent_address` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `reset_request` tinyint(4) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `gender`, `email`, `fcm_id`, `device_type`, `email_verified_at`, `password`, `mobile`, `image`, `dob`, `current_address`, `permanent_address`, `status`, `reset_request`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'super', 'admin', 'Male', 'admin@gmail.com', NULL, NULL, NULL, '$2y$10$cWfChfTmcY2.VscDG.Gfbu.l1HZG2m8g5PtNrP3fzlm4mseSorCL.', '', 'logo.svg', NULL, NULL, NULL, 1, 0, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(2, 'father', 'account', 'Male', 'father@gmail.com', NULL, NULL, NULL, '$2y$10$/zVefVPLCYPyxxDok2mK1u/qeTFGAgdQAmaZJDY57Kpj8O2p32zXG', '1234567890', 'parents/user.png', NULL, 'Cairo', 'Cairo', 1, 0, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(3, 'Mother', 'Account', 'Female', 'mother@gmail.com', NULL, NULL, NULL, '$2y$10$xXkt/32UCvvhkL1aaWxvZuN7LzL1gZxPZQdpIvW0QY7nwWLQbjr9y', '1234567890', 'parents/user.png', NULL, 'cairo', 'cairo', 1, 0, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL),
(4, 'Student', 'Account', 'Male', 'student@gmail.com', NULL, NULL, NULL, '$2y$10$eE4R1rN5U7CAx1oosWusiO5m4eKOxzgyUgwjN3O0Nl5yVnbqdvsFC', '1234567890', 'students/user.png', NULL, 'cairo', 'cairo', 1, 0, NULL, '2024-07-29 04:22:01', '2024-07-29 04:22:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_settings`
--

CREATE TABLE `web_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `tag` varchar(191) NOT NULL,
  `heading` varchar(191) NOT NULL,
  `content` varchar(500) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_settings`
--

INSERT INTO `web_settings` (`id`, `name`, `tag`, `heading`, `content`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'about_us', 'About Us', 'Cutting-Edge Education That Empowers', 'Lorem ipsum dolor sit amet consectetur. Faucibus non mauris risus enim sed. Lectus fusce elit duis dignissim aliquet nisl vitae. Eget sit nisi vulputate enim sem. Facilisi tincidunt donec interdum in in eros quisque consectetur sit. Sagittis purus velit amet risus egestas sed arcu nam. Pellentesque pharetra blandit fringilla volutpat tristique sit. Sit euismod praesent volutpat eu et. Id egestas dictum cursus purus morbi semper praesent quam. Facilisis mattis amet consectetur enim aliquam. Id se', 'websettings/content/aboutus.png', 1, NULL, NULL),
(2, 'who_we_are', 'Who we are', 'Empowering Minds, Shaping Futures', 'Lorem ipsum dolor sit amet consectetur. Nunc vel vehicula turpis ac tristique sit condimentum in. Amet ac egestas in commodo sed at. Amet dis sit porttitor sed suspendisse viverra dolor.Gravida non neque ac vitae semper nisi. Sapien quis tempor facilisis sed tincidunt sapien. Lobortis sollicitudin mi dolor aliquam ultricies.', 'websettings/content/whoweare.png', 1, NULL, NULL),
(3, 'teacher', 'Our Expert Teacher', 'More Than Just Teachers, They are Mentors', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(4, 'events', 'Our Events and News', 'Don`t Miss the Biggest Events and News of the Year!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(5, 'programs', 'Educational Programs', 'Educational Programs for every Stage', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(6, 'photos', 'Our Photos', 'Capturing Memories,Building Dreams', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(7, 'videos', 'Our Videos', 'Rewind, Replay, Rejoice! Dive into Our Video Vault', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(8, 'faqs', 'FAQs', 'Got Questions? We have Got Answers! Dive into Our FAQs', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(9, 'app', 'Download Our School Apps!', 'Empower Everyone: Teachers, Students, Parents - Get the App Now!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, 1, NULL, NULL),
(10, 'question', 'Got a Question?', 'Admissions, Academics, Support:Find Your Answer Here!', NULL, NULL, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendars`
--
ALTER TABLE `academic_calendars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_table_type_table_id_index` (`table_type`,`table_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_files`
--
ALTER TABLE `chat_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_files_message_id_foreign` (`message_id`);

--
-- Indexes for table `chat_members`
--
ALTER TABLE `chat_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_members_chat_room_id_foreign` (`chat_room_id`),
  ADD KEY `chat_members_user_id_foreign` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_modal_type_modal_id_index` (`modal_type`,`modal_id`),
  ADD KEY `chat_messages_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_stream_id_foreign` (`stream_id`),
  ADD KEY `classes_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `class_sections`
--
ALTER TABLE `class_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_teachers_class_section_id_foreign` (`class_section_id`),
  ADD KEY `class_teachers_class_teacher_id_foreign` (`class_teacher_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_teacher_id_foreign` (`teacher_id`),
  ADD KEY `coupons_only_applied_to_type_only_applied_to_id_index` (`only_applied_to_type`,`only_applied_to_id`),
  ADD KEY `coupons_class_id_foreign` (`class_id`),
  ADD KEY `coupons_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_usages_coupon_id_foreign` (`coupon_id`),
  ADD KEY `coupon_usages_used_by_user_type_used_by_user_id_index` (`used_by_user_type`,`used_by_user_id`),
  ADD KEY `coupon_usages_applied_to_type_applied_to_id_index` (`applied_to_type`,`applied_to_id`);

--
-- Indexes for table `educational_programs`
--
ALTER TABLE `educational_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elective_subject_groups`
--
ALTER TABLE `elective_subject_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_lesson_id_foreign` (`lesson_id`),
  ADD KEY `enrollments_user_id_foreign` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_classes`
--
ALTER TABLE `exam_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_marks`
--
ALTER TABLE `exam_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_timetables`
--
ALTER TABLE `exam_timetables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_choiceables`
--
ALTER TABLE `fees_choiceables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_choiceables_payment_transaction_id_index` (`payment_transaction_id`);

--
-- Indexes for table `fees_classes`
--
ALTER TABLE `fees_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_paids`
--
ALTER TABLE `fees_paids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_types`
--
ALTER TABLE `fees_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_modal_type_modal_id_index` (`modal_type`,`modal_id`);

--
-- Indexes for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `installment_fees`
--
ALTER TABLE `installment_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `installment_fees_session_year_id_index` (`session_year_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_user_id_foreign` (`user_id`),
  ADD KEY `leaves_leave_master_id_foreign` (`leave_master_id`);

--
-- Indexes for table `leave_details`
--
ALTER TABLE `leave_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_details_leave_id_foreign` (`leave_id`);

--
-- Indexes for table `leave_masters`
--
ALTER TABLE `leave_masters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_masters_session_year_id_foreign` (`session_year_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `lesson_topics`
--
ALTER TABLE `lesson_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_files_media_id_foreign` (`media_id`);

--
-- Indexes for table `mediums`
--
ALTER TABLE `mediums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `multiple_events`
--
ALTER TABLE `multiple_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `multiple_events_event_id_foreign` (`event_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_exams`
--
ALTER TABLE `online_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exams_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `online_exams_subject_id_index` (`subject_id`),
  ADD KEY `online_exams_session_year_id_index` (`session_year_id`);

--
-- Indexes for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_questions_class_subject_id_index` (`class_subject_id`);

--
-- Indexes for table `online_exam_question_answers`
--
ALTER TABLE `online_exam_question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_question_answers_question_id_index` (`question_id`),
  ADD KEY `online_exam_question_answers_answer_index` (`answer`);

--
-- Indexes for table `online_exam_question_choices`
--
ALTER TABLE `online_exam_question_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_question_choices_online_exam_id_index` (`online_exam_id`),
  ADD KEY `online_exam_question_choices_question_id_index` (`question_id`);

--
-- Indexes for table `online_exam_question_options`
--
ALTER TABLE `online_exam_question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_question_options_question_id_index` (`question_id`);

--
-- Indexes for table `online_exam_student_answers`
--
ALTER TABLE `online_exam_student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_exam_student_answers_student_id_index` (`student_id`),
  ADD KEY `online_exam_student_answers_online_exam_id_index` (`online_exam_id`),
  ADD KEY `online_exam_student_answers_question_id_index` (`question_id`),
  ADD KEY `online_exam_student_answers_option_id_index` (`option_id`);

--
-- Indexes for table `paid_installment_fees`
--
ALTER TABLE `paid_installment_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paid_installment_fees_class_id_index` (`class_id`),
  ADD KEY `paid_installment_fees_student_id_index` (`student_id`),
  ADD KEY `paid_installment_fees_parent_id_index` (`parent_id`),
  ADD KEY `paid_installment_fees_installment_fee_id_index` (`installment_fee_id`),
  ADD KEY `paid_installment_fees_session_year_id_index` (`session_year_id`),
  ADD KEY `paid_installment_fees_payment_transaction_id_index` (`payment_transaction_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `read_messages`
--
ALTER TABLE `read_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `read_messages_modal_type_modal_id_index` (`modal_type`,`modal_id`),
  ADD KEY `read_messages_user_id_foreign` (`user_id`),
  ADD KEY `read_messages_last_read_message_id_foreign` (`last_read_message_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_years`
--
ALTER TABLE `session_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staffs_user_id_foreign` (`user_id`);

--
-- Indexes for table `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_online_exam_statuses`
--
ALTER TABLE `student_online_exam_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_online_exam_statuses_student_id_index` (`student_id`),
  ADD KEY `student_online_exam_statuses_online_exam_id_index` (`online_exam_id`);

--
-- Indexes for table `student_sessions`
--
ALTER TABLE `student_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_teachers`
--
ALTER TABLE `subject_teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_notifications_notification_id_foreign` (`notification_id`),
  ADD KEY `user_notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `web_settings`
--
ALTER TABLE `web_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendars`
--
ALTER TABLE `academic_calendars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_files`
--
ALTER TABLE `chat_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_members`
--
ALTER TABLE `chat_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_sections`
--
ALTER TABLE `class_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `class_subjects`
--
ALTER TABLE `class_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_teachers`
--
ALTER TABLE `class_teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `educational_programs`
--
ALTER TABLE `educational_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `elective_subject_groups`
--
ALTER TABLE `elective_subject_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_classes`
--
ALTER TABLE `exam_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_marks`
--
ALTER TABLE `exam_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_timetables`
--
ALTER TABLE `exam_timetables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_choiceables`
--
ALTER TABLE `fees_choiceables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_classes`
--
ALTER TABLE `fees_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_paids`
--
ALTER TABLE `fees_paids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_types`
--
ALTER TABLE `fees_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `form_fields`
--
ALTER TABLE `form_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `installment_fees`
--
ALTER TABLE `installment_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_details`
--
ALTER TABLE `leave_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_masters`
--
ALTER TABLE `leave_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lesson_topics`
--
ALTER TABLE `lesson_topics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medias`
--
ALTER TABLE `medias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mediums`
--
ALTER TABLE `mediums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `multiple_events`
--
ALTER TABLE `multiple_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exams`
--
ALTER TABLE `online_exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_question_answers`
--
ALTER TABLE `online_exam_question_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_question_choices`
--
ALTER TABLE `online_exam_question_choices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_question_options`
--
ALTER TABLE `online_exam_question_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exam_student_answers`
--
ALTER TABLE `online_exam_student_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paid_installment_fees`
--
ALTER TABLE `paid_installment_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `read_messages`
--
ALTER TABLE `read_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `session_years`
--
ALTER TABLE `session_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `streams`
--
ALTER TABLE `streams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_online_exam_statuses`
--
ALTER TABLE `student_online_exam_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_sessions`
--
ALTER TABLE `student_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subject_teachers`
--
ALTER TABLE `subject_teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_settings`
--
ALTER TABLE `web_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_files`
--
ALTER TABLE `chat_files`
  ADD CONSTRAINT `chat_files_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_members`
--
ALTER TABLE `chat_members`
  ADD CONSTRAINT `chat_members_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `classes_stream_id_foreign` FOREIGN KEY (`stream_id`) REFERENCES `streams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `class_teachers`
--
ALTER TABLE `class_teachers`
  ADD CONSTRAINT `class_teachers_class_section_id_foreign` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_teachers_class_teacher_id_foreign` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coupons_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coupons_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD CONSTRAINT `coupon_usages_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`),
  ADD CONSTRAINT `enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `fees_choiceables`
--
ALTER TABLE `fees_choiceables`
  ADD CONSTRAINT `fees_choiceables_payment_transaction_id_foreign` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `installment_fees`
--
ALTER TABLE `installment_fees`
  ADD CONSTRAINT `installment_fees_session_year_id_foreign` FOREIGN KEY (`session_year_id`) REFERENCES `session_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_leave_master_id_foreign` FOREIGN KEY (`leave_master_id`) REFERENCES `leave_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leave_details`
--
ALTER TABLE `leave_details`
  ADD CONSTRAINT `leave_details_leave_id_foreign` FOREIGN KEY (`leave_id`) REFERENCES `leaves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leave_masters`
--
ALTER TABLE `leave_masters`
  ADD CONSTRAINT `leave_masters_session_year_id_foreign` FOREIGN KEY (`session_year_id`) REFERENCES `session_years` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `media_files`
--
ALTER TABLE `media_files`
  ADD CONSTRAINT `media_files_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `medias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `multiple_events`
--
ALTER TABLE `multiple_events`
  ADD CONSTRAINT `multiple_events_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `online_exams`
--
ALTER TABLE `online_exams`
  ADD CONSTRAINT `online_exams_session_year_id_foreign` FOREIGN KEY (`session_year_id`) REFERENCES `session_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  ADD CONSTRAINT `online_exam_questions_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_question_answers`
--
ALTER TABLE `online_exam_question_answers`
  ADD CONSTRAINT `online_exam_question_answers_answer_foreign` FOREIGN KEY (`answer`) REFERENCES `online_exam_question_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_question_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `online_exam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_question_choices`
--
ALTER TABLE `online_exam_question_choices`
  ADD CONSTRAINT `online_exam_question_choices_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_question_choices_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `online_exam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_question_options`
--
ALTER TABLE `online_exam_question_options`
  ADD CONSTRAINT `online_exam_question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `online_exam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_student_answers`
--
ALTER TABLE `online_exam_student_answers`
  ADD CONSTRAINT `online_exam_student_answers_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_student_answers_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `online_exam_question_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_student_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `online_exam_question_choices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_exam_student_answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paid_installment_fees`
--
ALTER TABLE `paid_installment_fees`
  ADD CONSTRAINT `paid_installment_fees_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paid_installment_fees_installment_fee_id_foreign` FOREIGN KEY (`installment_fee_id`) REFERENCES `installment_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paid_installment_fees_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paid_installment_fees_payment_transaction_id_foreign` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paid_installment_fees_session_year_id_foreign` FOREIGN KEY (`session_year_id`) REFERENCES `session_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paid_installment_fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `read_messages`
--
ALTER TABLE `read_messages`
  ADD CONSTRAINT `read_messages_last_read_message_id_foreign` FOREIGN KEY (`last_read_message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `read_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staffs`
--
ALTER TABLE `staffs`
  ADD CONSTRAINT `staffs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `student_online_exam_statuses`
--
ALTER TABLE `student_online_exam_statuses`
  ADD CONSTRAINT `student_online_exam_statuses_online_exam_id_foreign` FOREIGN KEY (`online_exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_online_exam_statuses_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
