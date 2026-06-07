<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student Profile</title>
</head>
<body>
    <h1>Modify Student Record Details</h1>
    <p><a href="{{ route('students.index') }}">← Cancel & Return</a></p>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT') <div style="margin-bottom: 10px;">
            <label for="user_id">User Account ID Reference:</label><br>
            <input type="number" id="user_id" name="user_id" value="{{ old('user_id', $student->user_id) }}" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="student_number">Student Number:</label><br>
            <input type="text" id="student_number" name="student_number" value="{{ old('student_number', $student->student_number) }}" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="phone">Phone Number:</label><br>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="date_of_birth">Date of Birth:</label><br>
            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth) }}">
        </div>

        <button type="submit">Update Student Record</button>
    </form>
</body>
</html>