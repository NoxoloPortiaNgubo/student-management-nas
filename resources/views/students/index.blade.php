<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory</title>
</head>
<body>
    <h1>Student Management Directory</h1>

    @if(session('success'))
        <div style="color: green; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 15px;">
        <a href="{{ route('students.create') }}">+ Add New Student Record</a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Student Number</th>
                <th>Phone</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->phone ?? 'N/A' }}</td>
                    <td>{{ $student->date_of_birth ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('students.edit', $student->id) }}" style="margin-right: 10px;">Edit</a>

                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No student records discovered in the system database.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        {{ $students->links() }}
    </div>
</body>
</html>